<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Character;
use App\Models\Choice;
use App\Models\Post;
use App\Models\PrivateMessage;
use App\Models\Reaction;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create categories
        $categories = [
            ['name' => 'General Discussion', 'description' => 'Talk about anything simulation-related', 'sort_order' => 1],
            ['name' => 'Simulation Reviews', 'description' => 'Share your experiences with different simulations', 'sort_order' => 2],
            ['name' => 'Developer Corner', 'description' => 'Discussions about simulation development', 'sort_order' => 3],
            ['name' => 'Technical Support', 'description' => 'Help with simulation issues', 'sort_order' => 4],
        ];

        foreach ($categories as $cat) {
            Category::create([
                'name' => $cat['name'],
                'slug' => Str::slug($cat['name']),
                'description' => $cat['description'],
                'sort_order' => $cat['sort_order'],
            ]);
        }

        // Create characters
        $characters = [
            [
                'username' => 'nova_seeker',
                'display_name' => 'Nova Seeker',
                'role_title' => 'Simulation Enthusiast',
                'signature' => 'Reality is just the most persistent simulation.',
                'bio' => 'Long-time simulation explorer. Particularly interested in reality-bending experiences.',
                'fake_join_date' => now()->subYears(3),
            ],
            [
                'username' => 'mindstate_archivist',
                'display_name' => 'The Archivist',
                'role_title' => 'Mind State Preservationist',
                'signature' => 'Every experience matters.',
                'bio' => 'I collect and preserve interesting simulation experiences for posterity.',
                'fake_join_date' => now()->subYears(5),
            ],
            [
                'username' => 'quantum_drifter',
                'display_name' => 'Quantum Drifter',
                'role_title' => 'Reality Explorer',
                'signature' => 'Between the ones and zeros, there is infinite possibility.',
                'bio' => 'Explorer of edge-case simulations and reality boundaries.',
                'fake_join_date' => now()->subYears(2),
            ],
            [
                'username' => 'synaptic_surge',
                'display_name' => 'Synaptic Surge',
                'role_title' => 'Newcomer',
                'signature' => null,
                'bio' => 'Just started exploring simulations. Excited to learn!',
                'fake_join_date' => now()->subMonths(3),
            ],
        ];

        foreach ($characters as $char) {
            Character::create($char);
        }

        $nova = Character::where('username', 'nova_seeker')->first();
        $archivist = Character::where('username', 'mindstate_archivist')->first();
        $quantum = Character::where('username', 'quantum_drifter')->first();
        $synaptic = Character::where('username', 'synaptic_surge')->first();

        // Create threads and posts
        $generalCat = Category::where('slug', 'general-discussion')->first();
        $reviewsCat = Category::where('slug', 'simulation-reviews')->first();

        // Thread 1
        $thread1 = Thread::create([
            'category_id' => $generalCat->id,
            'author_id' => $nova->id,
            'title' => 'Welcome to SimForums Unlimited!',
            'slug' => 'welcome-to-simforums-unlimited-' . Str::random(6),
            'fake_created_at' => now()->subDays(30),
            'is_pinned' => true,
        ]);

        Post::create([
            'thread_id' => $thread1->id,
            'author_id' => $nova->id,
            'content' => "Welcome, fellow simulation enthusiasts!\n\nThis is a space for all of us who explore the vast landscape of simulations - whether you're a seasoned explorer or just starting your journey.\n\nFeel free to share your experiences, ask questions, and connect with others who share our passion for simulated realities.\n\nRemember: In a post-scarcity society, we explore not because we must, but because we can. Every simulation offers a new perspective, a new way of being.\n\nEnjoy your stay!",
            'fake_created_at' => now()->subDays(30),
        ]);

        Post::create([
            'thread_id' => $thread1->id,
            'author_id' => $archivist->id,
            'content' => "Thank you for creating this space, Nova!\n\nI've been archiving simulation experiences for many cycles now, and it's wonderful to have a community to share them with.\n\nLooking forward to many fascinating discussions.",
            'fake_created_at' => now()->subDays(29),
        ]);

        // Thread 2 with a choice
        $thread2 = Thread::create([
            'category_id' => $reviewsCat->id,
            'author_id' => $quantum->id,
            'title' => 'The Infinite Library - A Mind-Bending Experience',
            'slug' => 'the-infinite-library-' . Str::random(6),
            'fake_created_at' => now()->subDays(15),
        ]);

        $post2 = Post::create([
            'thread_id' => $thread2->id,
            'author_id' => $quantum->id,
            'content' => "Just emerged from 'The Infinite Library' simulation and I need to process what I experienced.\n\nThe premise is simple: an endless library containing every book that could ever be written. But the execution... incredible.\n\nI found myself lost in corridors that seemed to stretch into dimensions I couldn't comprehend. Some books contained my own memories, written before I lived them. Others held conversations I would have in the future.\n\nThe most unsettling part was finding a book titled with my name, containing everything I would ever think or do. I had a choice then - to read it or close it forever.\n\nWhat would you do?",
            'fake_created_at' => now()->subDays(15),
        ]);

        // Create a choice for this post
        $choice = Choice::create([
            'trigger_post_id' => $post2->id,
            'prompt_text' => 'What would you do with a book containing your entire existence?',
            'description' => 'This choice affects which perspectives you\'ll see in the following discussion.',
            'identifier' => 'infinite-library-choice',
        ]);

        $option1 = $choice->options()->create([
            'label' => 'Read the book',
            'description' => 'Knowledge is worth any price.',
            'sort_order' => 0,
        ]);

        $option2 = $choice->options()->create([
            'label' => 'Close it and walk away',
            'description' => 'Some mysteries should remain unsolved.',
            'sort_order' => 1,
        ]);

        // Conditional posts removed - use phase-based visibility system instead
        // These posts were previously gated by choice options, but that system has been replaced
        // with phase-based visibility. Recreate them using phases if needed.

        // Response visible to everyone
        Post::create([
            'thread_id' => $thread2->id,
            'author_id' => $synaptic->id,
            'content' => "This sounds incredible! Where can I find this simulation? I'm still new to all this but I really want to try it.\n\nAlso, is it scary? I'm not great with horror experiences...",
            'fake_created_at' => now()->subDays(13),
        ]);

        // Add some reactions
        Reaction::create([
            'post_id' => $post2->id,
            'character_id' => $nova->id,
            'type' => 'insightful',
            'fake_created_at' => now()->subDays(14),
        ]);

        Reaction::create([
            'post_id' => $post2->id,
            'character_id' => $archivist->id,
            'type' => 'like',
            'fake_created_at' => now()->subDays(14),
        ]);

        // Create a private message
        PrivateMessage::create([
            'sender_id' => $archivist->id,
            'recipient_id' => $quantum->id,
            'subject' => 'About your Infinite Library experience',
            'content' => "Quantum,\n\nYour post about the Infinite Library resonated deeply with me. I've been collecting experiences like this for preservation.\n\nWould you be willing to share your full mind-state recording of the experience? I'd like to add it to my archive of significant simulation moments.\n\nOf course, I would maintain complete discretion about its contents.\n\nWarm regards,\nThe Archivist",
            'fake_sent_at' => now()->subDays(14),
            'is_read' => true,
        ]);

        PrivateMessage::create([
            'sender_id' => $quantum->id,
            'recipient_id' => $archivist->id,
            'subject' => 'Re: About your Infinite Library experience',
            'content' => "Archivist,\n\nI'd be honored to contribute to your collection. The experience was profound enough that I believe it deserves preservation.\n\nI'll prepare a sanitized version of the mind-state recording - there are some deeply personal moments I'd prefer to keep private.\n\nExpect it within the next few cycles.\n\n- Q",
            'fake_sent_at' => now()->subDays(13),
            'is_read' => false,
        ]);

        // Update post counts
        foreach (Character::all() as $char) {
            $char->updatePostCount();
        }
    }
}
