<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Character;
use App\Models\Choice;
use App\Models\ChoiceOption;
use App\Models\ContentTrigger;
use App\Models\Phase;
use App\Models\Post;
use App\Models\PrivateMessage;
use App\Models\Thread;
use App\Models\TriggerCondition;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StoryFlowController extends Controller
{
    /**
     * Display the story flow editor
     */
    public function index()
    {
        $categories = Category::orderBy('sort_order')->get();
        $characters = Character::orderBy('username')->get();
        $triggers = ContentTrigger::with('conditions')->orderBy('name')->get();
        $phases = Phase::orderBy('sort_order')->get();

        return view('admin.story-flow.index', compact('categories', 'characters', 'triggers', 'phases'));
    }

    /**
     * Get graph data as JSON
     */
    public function data()
    {
        $nodes = [];
        $edges = [];

        // Categories
        $categories = Category::orderBy('sort_order')->get();
        foreach ($categories as $category) {
            $nodes[] = [
                'id' => "category_{$category->id}",
                'label' => $category->name,
                'type' => 'category',
                'data' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'description' => $category->description,
                    'slug' => $category->slug,
                    'sort_order' => $category->sort_order,
                ],
            ];
        }

        // Threads
        $threads = Thread::with(['category', 'author', 'phase'])->get();
        foreach ($threads as $thread) {
            $nodes[] = [
                'id' => "thread_{$thread->id}",
                'label' => Str::limit($thread->title, 30),
                'type' => 'thread',
                'data' => [
                    'id' => $thread->id,
                    'title' => $thread->title,
                    'slug' => $thread->slug,
                    'category_id' => $thread->category_id,
                    'author_id' => $thread->author_id,
                    'author_name' => $thread->author?->display_name,
                    'fake_created_at' => $thread->fake_created_at?->format('Y-m-d H:i'),
                    'is_pinned' => $thread->is_pinned,
                    'is_locked' => $thread->is_locked,
                    'phase_id' => $thread->phase_id,
                ],
            ];

            // Edge: Category contains Thread
            $edges[] = [
                'from' => "category_{$thread->category_id}",
                'to' => "thread_{$thread->id}",
                'type' => 'contains',
                'dashes' => true,
            ];

            // Edge: Phase unlocks Thread
            if ($thread->phase_id) {
                $edges[] = [
                    'from' => "phase_{$thread->phase_id}",
                    'to' => "thread_{$thread->id}",
                    'type' => 'unlocks',
                    'color' => '#8b5cf6',
                ];
            }
        }

        // Posts
        $posts = Post::with(['thread', 'author', 'choice', 'phase'])->get();
        foreach ($posts as $post) {
            $nodes[] = [
                'id' => "post_{$post->id}",
                'label' => Str::limit(strip_tags($post->content), 25),
                'type' => 'post',
                'data' => [
                    'id' => $post->id,
                    'thread_id' => $post->thread_id,
                    'thread_title' => $post->thread?->title,
                    'author_id' => $post->author_id,
                    'author_name' => $post->author?->display_name,
                    'content' => $post->content,
                    'fake_created_at' => $post->fake_created_at?->format('Y-m-d H:i'),
                    'has_choice' => $post->choice !== null,
                    'phase_id' => $post->phase_id,
                ],
            ];

            // Edge: Thread contains Post
            $edges[] = [
                'from' => "thread_{$post->thread_id}",
                'to' => "post_{$post->id}",
                'type' => 'contains',
                'dashes' => true,
            ];

            // Edge: Phase unlocks Post
            if ($post->phase_id) {
                $edges[] = [
                    'from' => "phase_{$post->phase_id}",
                    'to' => "post_{$post->id}",
                    'type' => 'unlocks',
                    'color' => '#8b5cf6',
                ];
            }
        }

        // Choices and Options
        $choices = Choice::with('options', 'triggerPost')->get();
        foreach ($choices as $choice) {
            $nodes[] = [
                'id' => "choice_{$choice->id}",
                'label' => Str::limit($choice->prompt_text, 25),
                'type' => 'choice',
                'data' => [
                    'id' => $choice->id,
                    'prompt_text' => $choice->prompt_text,
                    'type' => $choice->type,
                    'identifier' => $choice->identifier,
                    'trigger_post_id' => $choice->trigger_post_id,
                    'description' => $choice->description,
                ],
            ];

            // Edge: Post has Choice
            if ($choice->trigger_post_id) {
                $edges[] = [
                    'from' => "post_{$choice->trigger_post_id}",
                    'to' => "choice_{$choice->id}",
                    'type' => 'has_choice',
                    'color' => '#3b82f6',
                ];
            }

            // Choice Options
            foreach ($choice->options as $option) {
                $nodes[] = [
                    'id' => "choice_option_{$option->id}",
                    'label' => Str::limit($option->label, 20),
                    'type' => 'choice_option',
                    'data' => [
                        'id' => $option->id,
                        'choice_id' => $option->choice_id,
                        'label' => $option->label,
                        'description' => $option->description,
                        'spawned_post_id' => $option->spawned_post_id,
                    ],
                ];

                // Edge: Choice has Option
                $edges[] = [
                    'from' => "choice_{$choice->id}",
                    'to' => "choice_option_{$option->id}",
                    'type' => 'has_option',
                    'color' => '#8b5cf6',
                ];

                // Edge: Option spawns Post
                if ($option->spawned_post_id) {
                    $edges[] = [
                        'from' => "choice_option_{$option->id}",
                        'to' => "post_{$option->spawned_post_id}",
                        'type' => 'spawns',
                        'color' => '#10b981',
                        'dashes' => [5, 5],
                    ];
                }
            }
        }

        // Triggers
        $triggers = ContentTrigger::with('conditions')->get();
        foreach ($triggers as $trigger) {
            $nodes[] = [
                'id' => "trigger_{$trigger->id}",
                'label' => Str::limit($trigger->name, 25),
                'type' => 'trigger',
                'data' => [
                    'id' => $trigger->id,
                    'name' => $trigger->name,
                    'identifier' => $trigger->identifier,
                    'description' => $trigger->getRawOriginal('description'),
                    'conditions_count' => $trigger->conditions->count(),
                ],
            ];

            // Edges for trigger conditions
            foreach ($trigger->conditions as $condition) {
                if ($condition->type === TriggerCondition::TYPE_VIEW_POST && $condition->target_id) {
                    $edges[] = [
                        'from' => "post_{$condition->target_id}",
                        'to' => "trigger_{$trigger->id}",
                        'type' => 'activates',
                        'color' => '#f59e0b',
                        'dashes' => [2, 2],
                    ];
                } elseif ($condition->type === TriggerCondition::TYPE_VIEW_THREAD && $condition->target_id) {
                    $edges[] = [
                        'from' => "thread_{$condition->target_id}",
                        'to' => "trigger_{$trigger->id}",
                        'type' => 'activates',
                        'color' => '#f59e0b',
                        'dashes' => [2, 2],
                    ];
                } elseif ($condition->type === TriggerCondition::TYPE_REACT_POST && $condition->target_id) {
                    $edges[] = [
                        'from' => "post_{$condition->target_id}",
                        'to' => "trigger_{$trigger->id}",
                        'type' => 'activates_react',
                        'color' => '#ec4899',
                        'dashes' => [2, 2],
                    ];
                } elseif ($condition->type === TriggerCondition::TYPE_CHOICE && $condition->choice_option_id) {
                    $edges[] = [
                        'from' => "choice_option_{$condition->choice_option_id}",
                        'to' => "trigger_{$trigger->id}",
                        'type' => 'activates',
                        'color' => '#8b5cf6',
                        'dashes' => [2, 2],
                    ];
                }
            }
        }

        // Private Messages
        $messages = PrivateMessage::with(['sender', 'recipient', 'phase'])->get();
        foreach ($messages as $message) {
            $nodes[] = [
                'id' => "message_{$message->id}",
                'label' => Str::limit($message->subject, 25),
                'type' => 'message',
                'data' => [
                    'id' => $message->id,
                    'subject' => $message->subject,
                    'content' => $message->content,
                    'sender_id' => $message->sender_id,
                    'sender_name' => $message->sender?->display_name,
                    'recipient_id' => $message->recipient_id,
                    'recipient_name' => $message->recipient?->display_name,
                    'fake_sent_at' => $message->fake_sent_at?->format('Y-m-d H:i'),
                    'phase_id' => $message->phase_id,
                ],
            ];

            // Edge: Phase unlocks Message
            if ($message->phase_id) {
                $edges[] = [
                    'from' => "phase_{$message->phase_id}",
                    'to' => "message_{$message->id}",
                    'type' => 'unlocks',
                    'color' => '#8b5cf6',
                ];
            }
        }

        return response()->json([
            'nodes' => $nodes,
            'edges' => $edges,
        ]);
    }

    /**
     * Create a new node
     */
    public function storeNode(Request $request)
    {
        $type = $request->input('type');

        switch ($type) {
            case 'category':
                $validated = $request->validate([
                    'name' => 'required|string|max:255',
                    'description' => 'nullable|string',
                ]);
                $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(6);
                $validated['sort_order'] = Category::max('sort_order') + 1;
                $item = Category::create($validated);
                break;

            case 'thread':
                $validated = $request->validate([
                    'title' => 'required|string|max:255',
                    'category_id' => 'required|exists:categories,id',
                    'author_id' => 'required|exists:characters,id',
                    'fake_created_at' => 'nullable|date',
                    'first_post_content' => 'nullable|string',
                ]);
                $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(6);
                $firstPostContent = $validated['first_post_content'] ?? '<p>New post content...</p>';
                unset($validated['first_post_content']);
                $item = Thread::create($validated);
                // Create first post
                $item->posts()->create([
                    'author_id' => $validated['author_id'],
                    'content' => $firstPostContent,
                    'fake_created_at' => $validated['fake_created_at'] ?? now(),
                ]);
                break;

            case 'post':
                $validated = $request->validate([
                    'thread_id' => 'required|exists:threads,id',
                    'author_id' => 'required|exists:characters,id',
                    'content' => 'nullable|string',
                    'fake_created_at' => 'nullable|date',
                ]);
                $validated['content'] = $validated['content'] ?? '<p>New post content...</p>';
                $item = Post::create($validated);
                break;

            case 'choice':
                $validated = $request->validate([
                    'prompt_text' => 'required|string|max:255',
                    'type' => 'required|in:choice,poll',
                    'trigger_post_id' => 'nullable|exists:posts,id',
                    'options' => 'required|array|min:2',
                    'options.*.label' => 'required|string|max:255',
                ]);
                $validated['identifier'] = Str::slug($validated['prompt_text']) . '-' . Str::random(4);
                $options = $validated['options'];
                unset($validated['options']);
                $item = Choice::create($validated);
                foreach ($options as $i => $opt) {
                    $item->options()->create([
                        'label' => $opt['label'],
                        'sort_order' => $i,
                    ]);
                }
                break;

            case 'trigger':
                $validated = $request->validate([
                    'name' => 'required|string|max:255',
                    'description' => 'nullable|string',
                ]);
                $validated['identifier'] = Str::slug($validated['name']) . '-' . Str::random(6);
                $item = ContentTrigger::create($validated);
                break;

            case 'message':
                $validated = $request->validate([
                    'subject' => 'required|string|max:255',
                    'content' => 'nullable|string',
                    'sender_id' => 'required|exists:characters,id',
                    'recipient_id' => 'required|exists:characters,id',
                    'fake_sent_at' => 'nullable|date',
                ]);
                $validated['content'] = $validated['content'] ?? '<p>Message content...</p>';
                $item = PrivateMessage::create($validated);
                break;

            default:
                return response()->json(['error' => 'Invalid node type'], 400);
        }

        return response()->json([
            'success' => true,
            'id' => $item->id,
            'node_id' => "{$type}_{$item->id}",
        ]);
    }

    /**
     * Update a node
     */
    public function updateNode(Request $request, string $type, int $id)
    {
        switch ($type) {
            case 'category':
                $item = Category::findOrFail($id);
                $validated = $request->validate([
                    'name' => 'sometimes|string|max:255',
                    'description' => 'nullable|string',
                    'sort_order' => 'sometimes|integer',
                ]);
                $item->update($validated);
                break;

            case 'thread':
                $item = Thread::findOrFail($id);
                $validated = $request->validate([
                    'title' => 'sometimes|string|max:255',
                    'category_id' => 'sometimes|exists:categories,id',
                    'author_id' => 'sometimes|exists:characters,id',
                    'fake_created_at' => 'nullable|date',
                    'is_pinned' => 'sometimes|boolean',
                    'is_locked' => 'sometimes|boolean',
                    'phase_id' => 'nullable|exists:phases,id',
                ]);
                $item->update($validated);
                break;

            case 'post':
                $item = Post::findOrFail($id);
                $validated = $request->validate([
                    'content' => 'sometimes|string',
                    'author_id' => 'sometimes|exists:characters,id',
                    'fake_created_at' => 'nullable|date',
                    'phase_id' => 'nullable|exists:phases,id',
                ]);
                $item->update($validated);
                break;

            case 'choice':
                $item = Choice::findOrFail($id);
                $validated = $request->validate([
                    'prompt_text' => 'sometimes|string|max:255',
                    'type' => 'sometimes|in:choice,poll',
                    'trigger_post_id' => 'nullable|exists:posts,id',
                    'description' => 'nullable|string',
                ]);
                $item->update($validated);
                break;

            case 'trigger':
                $item = ContentTrigger::findOrFail($id);
                $validated = $request->validate([
                    'name' => 'sometimes|string|max:255',
                    'description' => 'nullable|string',
                ]);
                $item->update($validated);
                break;

            case 'message':
                $item = PrivateMessage::findOrFail($id);
                $validated = $request->validate([
                    'subject' => 'sometimes|string|max:255',
                    'content' => 'sometimes|string',
                    'sender_id' => 'sometimes|exists:characters,id',
                    'recipient_id' => 'sometimes|exists:characters,id',
                    'fake_sent_at' => 'nullable|date',
                    'phase_id' => 'nullable|exists:phases,id',
                ]);
                $item->update($validated);
                break;

            default:
                return response()->json(['error' => 'Invalid node type'], 400);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Delete a node
     */
    public function destroyNode(string $type, int $id)
    {
        switch ($type) {
            case 'category':
                Category::findOrFail($id)->delete();
                break;
            case 'thread':
                Thread::findOrFail($id)->delete();
                break;
            case 'post':
                Post::findOrFail($id)->delete();
                break;
            case 'choice':
                Choice::findOrFail($id)->delete();
                break;
            case 'choice_option':
                ChoiceOption::findOrFail($id)->delete();
                break;
            case 'trigger':
                ContentTrigger::findOrFail($id)->delete();
                break;
            case 'message':
                PrivateMessage::findOrFail($id)->delete();
                break;
            default:
                return response()->json(['error' => 'Invalid node type'], 400);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Create an edge (connection between nodes)
     */
    public function storeEdge(Request $request)
    {
        $validated = $request->validate([
            'from_type' => 'required|string',
            'from_id' => 'required|integer',
            'to_type' => 'required|string',
            'to_id' => 'required|integer',
            'edge_type' => 'required|string',
        ]);

        $fromType = $validated['from_type'];
        $fromId = $validated['from_id'];
        $toType = $validated['to_type'];
        $toId = $validated['to_id'];
        $edgeType = $validated['edge_type'];

        // Handle different edge types
        if ($edgeType === 'unlocks' && $fromType === 'phase') {
            // Phase unlocks content
            if ($toType === 'thread') {
                Thread::where('id', $toId)->update(['phase_id' => $fromId]);
            } elseif ($toType === 'post') {
                Post::where('id', $toId)->update(['phase_id' => $fromId]);
            } elseif ($toType === 'message') {
                PrivateMessage::where('id', $toId)->update(['phase_id' => $fromId]);
            }
        } elseif ($edgeType === 'has_choice' && $fromType === 'post' && $toType === 'choice') {
            // Post has choice
            Choice::where('id', $toId)->update(['trigger_post_id' => $fromId]);
        } elseif ($edgeType === 'spawns' && $fromType === 'choice_option' && $toType === 'post') {
            // Choice option spawns post
            ChoiceOption::where('id', $fromId)->update(['spawned_post_id' => $toId]);
        } elseif ($edgeType === 'contains' && $fromType === 'category' && $toType === 'thread') {
            // Move thread to category
            Thread::where('id', $toId)->update(['category_id' => $fromId]);
        } elseif ($edgeType === 'contains' && $fromType === 'thread' && $toType === 'post') {
            // Move post to thread
            Post::where('id', $toId)->update(['thread_id' => $fromId]);
        } elseif ($edgeType === 'activates') {
            // Create trigger condition
            $trigger = ContentTrigger::findOrFail($toId);
            $conditionType = match($fromType) {
                'post' => TriggerCondition::TYPE_VIEW_POST,
                'thread' => TriggerCondition::TYPE_VIEW_THREAD,
                'choice_option' => TriggerCondition::TYPE_CHOICE,
                default => null,
            };
            
            if ($conditionType) {
                $trigger->conditions()->create([
                    'type' => $conditionType,
                    'target_type' => $fromType === 'choice_option' ? null : $fromType,
                    'target_id' => $fromType === 'choice_option' ? null : $fromId,
                    'choice_option_id' => $fromType === 'choice_option' ? $fromId : null,
                ]);
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Delete an edge
     */
    public function destroyEdge(Request $request)
    {
        $validated = $request->validate([
            'from_type' => 'required|string',
            'from_id' => 'required|integer',
            'to_type' => 'required|string',
            'to_id' => 'required|integer',
            'edge_type' => 'required|string',
        ]);

        $fromType = $validated['from_type'];
        $fromId = $validated['from_id'];
        $toType = $validated['to_type'];
        $toId = $validated['to_id'];
        $edgeType = $validated['edge_type'];

        if ($edgeType === 'unlocks' && $fromType === 'phase') {
            if ($toType === 'thread') {
                Thread::where('id', $toId)->where('phase_id', $fromId)->update(['phase_id' => null]);
            } elseif ($toType === 'post') {
                Post::where('id', $toId)->where('phase_id', $fromId)->update(['phase_id' => null]);
            } elseif ($toType === 'message') {
                PrivateMessage::where('id', $toId)->where('phase_id', $fromId)->update(['phase_id' => null]);
            }
        } elseif ($edgeType === 'has_choice' && $fromType === 'post' && $toType === 'choice') {
            Choice::where('id', $toId)->where('trigger_post_id', $fromId)->update(['trigger_post_id' => null]);
        } elseif ($edgeType === 'spawns' && $fromType === 'choice_option' && $toType === 'post') {
            ChoiceOption::where('id', $fromId)->where('spawned_post_id', $toId)->update(['spawned_post_id' => null]);
        } elseif ($edgeType === 'activates') {
            // Remove trigger condition
            $trigger = ContentTrigger::find($toId);
            if ($trigger) {
                if ($fromType === 'choice_option') {
                    $trigger->conditions()->where('choice_option_id', $fromId)->delete();
                } else {
                    $trigger->conditions()
                        ->where('target_type', $fromType)
                        ->where('target_id', $fromId)
                        ->delete();
                }
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Get timeline data
     */
    public function timeline()
    {
        $items = [];

        // Get all posts with timestamps
        $posts = Post::with(['thread.category', 'author'])
            ->whereNotNull('fake_created_at')
            ->orderBy('fake_created_at')
            ->get();

        foreach ($posts as $post) {
            $items[] = [
                'id' => "post_{$post->id}",
                'type' => 'post',
                'title' => Str::limit(strip_tags($post->content), 50),
                'subtitle' => $post->thread?->title,
                'category' => $post->thread?->category?->name,
                'author' => $post->author?->display_name,
                'date' => $post->fake_created_at->format('Y-m-d H:i'),
                'timestamp' => $post->fake_created_at->timestamp,
                'data' => $post->toArray(),
            ];
        }

        // Get all messages with timestamps
        $messages = PrivateMessage::with(['sender', 'recipient'])
            ->whereNotNull('fake_sent_at')
            ->orderBy('fake_sent_at')
            ->get();

        foreach ($messages as $message) {
            $items[] = [
                'id' => "message_{$message->id}",
                'type' => 'message',
                'title' => $message->subject,
                'subtitle' => "{$message->sender?->display_name} → {$message->recipient?->display_name}",
                'date' => $message->fake_sent_at->format('Y-m-d H:i'),
                'timestamp' => $message->fake_sent_at->timestamp,
                'data' => $message->toArray(),
            ];
        }

        // Sort by timestamp
        usort($items, fn($a, $b) => $a['timestamp'] <=> $b['timestamp']);

        return response()->json($items);
    }
}
