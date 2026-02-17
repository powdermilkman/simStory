<?php

namespace App\View\Components;

use App\Models\PrivateMessage;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class ForumLayout extends Component
{
    public int $unreadMessageCount = 0;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        if (Auth::guard('reader')->check()) {
            $reader = Auth::guard('reader')->user();
            $this->unreadMessageCount = PrivateMessage::inboxMessages()
                ->visibleTo($reader)
                ->where('is_read', false)
                ->count();
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('layouts.forum');
    }
}
