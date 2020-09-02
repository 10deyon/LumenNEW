<?php

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;

// use App\Todo;

// use Auth;

// class TodoController extends Controller {
    
//     public function __construct() {
//         $this->middleware('auth');
//     }
    
//     public function index(Request $request) {
//         $todo = Auth::user()->todo()->get();
//         return response()->json(['status' => 'success','result' => $todo]);
//     }

//     public function store(Request $request){
//         $this->validate($request, [
//             'todo' => 'required',
//             'description' => 'required',
//             'category' => 'required'
//         ]);
//         if(Auth::user()->todo()->Create($request->all())){
//             return response()->json(['status' => 'success']);
//         }else{
//             return response()->json(['status' => 'fail']);
//         }
//     }


//     public function show($id){
//         $todo = Todo::where('id', $id)->get();
//         return response()->json($todo);
//     }
    
//     public function edit($id) {
//         $todo = Todo::where('id', $id)->get();
//         return view('todo.edittodo',['todos' => $todo]);
//     }
    
//     public function update(Request $request, $id){
//         $this->validate($request, [
//             'todo' => 'filled',
//             'description' => 'filled',
//             'category' => 'filled'
//         ]);
        
//         $todo = Todo::find($id);
//         if($todo->fill($request->all())->save()){
//             return response()->json(['status' => 'success']);
//         }
//         return response()->json(['status' => 'failed']);
//     }
    
//     public function destroy($id){
//         if(Todo::destroy($id)){
//             return response()->json(['status' => 'success']);
//         }
//     }
// }