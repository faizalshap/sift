import React from 'react';
import _ from 'lodash';
import TodoListSidebar from './components/todo-list-sidebar';
import TodoList from './components/todo-list';
import CurrentTodoList from './components/current-todo-list';
import api from './lib/api';

export default class BigRocksApp extends React.Component {
  constructor(props) {
    super(props);
    this.state = {};
  }

  componentDidMount() {
    api.getTodoLists().then(todoLists => {
      this.setState({ todoLists: todoLists });
      this.showTodos(todoLists[0]);
    });

    api.getCurrentTodos().then(currentTodos => {
      this.setState({ currentTodos });
    });
  }

  showTodos(todoList) {
    this.setState({
      todos: undefined,
      todoList: todoList
    });
    api.getTodos(todoList.id).then(todos => this.setState({ todos: todos }));
  }

  addTodo(todoName) {
    let todo = {
      for_today: null,
      is_big_rock: false,
      name: todoName,
      percent_complete: 0,
      todolist_id: this.state.todoList.id,
      key: Math.round(Math.random() * 1000)
    };
    let oldTodos = this.state.todos;

    this.setState({ todos: [...oldTodos, todo] });

    api.addTodo(this.state.todoList.id, todo).then(persistedTodo => {
      this.setState({ todos: [...oldTodos, persistedTodo]});
    });
  }

  updateTodo(updatedTodo, sourceListName) {
    let oldTodos = this.state[sourceListName];
    this.setState({
      [sourceListName]: [
        ..._.reject(oldTodos, todo => todo.id == updatedTodo.id),
        updatedTodo
      ]
    });

    api.updateTodo(updatedTodo.todolist_id, updatedTodo.id, updatedTodo).catch(error => {
      this.setState({ [sourceListName]: oldTodos });
      alert('error: ' + error);
    });
  }

  render() {
    return (
      <div className='big-rocks-app'>
        <TodoListSidebar todoLists={this.state.todoLists} onClickList={this.showTodos.bind(this)} />
        <TodoList onAddTodo={this.addTodo.bind(this)} onUpdateTodo={this.updateTodo.bind(this)} todoList={this.state.todoList} todos={this.state.todos} />
        <CurrentTodoList currentTodos={this.state.currentTodos} onUpdateTodo={this.updateTodo.bind(this)} />
      </div>
    );
  }
};
