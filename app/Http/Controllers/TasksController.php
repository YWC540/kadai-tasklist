<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Task;    // 追加

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = [];
        if (\Auth::check()) { // 認証済みの場合
            // 認証済みユーザーを取得
            $userId = \Auth::id();
            // メッセージ一覧を取得
            $tasks = Task::where('user_id', $userId)->get();         // 追加

            // メッセージ一覧ビューでそれを表示
            return view('tasks.index', [     // 追加
            'tasks' => $tasks,        // 追加
            ]);                                 // 追加
        }

        // // メッセージ一覧を取得
        // $tasks = Task::all();         // 追加

        // // メッセージ一覧ビューでそれを表示
        // return view('tasks.index', [     // 追加
        //     'tasks' => $tasks,        // 追加
        // ]);                                 // 追加
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (\Auth::check()) {
            $task = new Task;

            // メッセージ作成ビューを表示
            return view('tasks.create', [
                'task' => $task,
            ]);
        }
        
        return redirect('/');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (\Auth::check()) {
            // バリデーション
            $request->validate([
                'status' => 'required|max:10',   // 追加
                'content' => 'required|max:255',
            ]);

            $user = \Auth::user();

            // メッセージを作成
            $task = new Task;
            $task->status = $request->status;    // 追加
            $task->content = $request->content;
            $task->user_id = $user->id;
            $task->save();

            // トップページへリダイレクトさせる
            return redirect('/');
        }
        
        return redirect('/');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // idの値でメッセージを検索して取得
        $task = Task::findOrFail($id);

        if ($task->user_id !== \Auth::id()) {
            return redirect('/');
        }
        
        // メッセージ詳細ビューでそれを表示
        return view('tasks.show', [
            'task' => $task,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // idの値でメッセージを検索して取得
        $task = Task::findOrFail($id);

        if ($task->user_id !== \Auth::id()) {
            return redirect('/');
        }

        // メッセージ編集ビューでそれを表示
        return view('tasks.edit', [
            'task' => $task,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // idの値でメッセージを検索して取得
        $task = Task::findOrFail($id);
        if ($task->user_id !== \Auth::id()) {
            return redirect('/');
        }

        // バリデーション
        $request->validate([
            'status' => 'required|max:10',   // 追加
            'content' => 'required|max:255',
        ]);

        
        $task->status = $request->status;    // 追加
        $task->content = $request->content;
        $task->save();

        // トップページへリダイレクトさせる
        return redirect('/');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // idの値でメッセージを検索して取得
        $task = Task::findOrFail($id);
        if ($task->user_id !== \Auth::id()) {
            return redirect('/');
        }

        // メッセージを削除
        $task->delete();

        // トップページへリダイレクトさせる
        return redirect('/');
    }
}
