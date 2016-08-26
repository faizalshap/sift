import React from 'react';
import _ from 'lodash';
import TodoListSidebar from './components/todo-list-sidebar';
import TodoList from './components/todo-list';
import CurrentTodoList from './components/current-todo-list';
import Api from './lib/api';
require('./styles/screen');
require('./styles/modules/header');
require('./styles/modules/content');

export default class BigRocksApp extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      isFullscreen: false
    };
    this.api = new Api(this.props.currentUser);
  }

  componentDidMount() {
    this.api.getTodoLists().then(todoLists => {
      this.setState({ todoLists: todoLists });
      this.showTodos(todoLists[0]);
    });

    this.api.getCurrentTodos().then(currentTodos => {
      this.setState({ currentTodos });
    });
  }

  showTodos(todoList) {
    this.setState({
      todos: undefined,
      todoList: todoList
    });
    this.api.getTodos(todoList.id).then(todos => this.setState({ todos: todos }));
  }

  addTodo(todoName) {
    let todo = {
      for_today: null,
      is_big_rock: false,
      name: todoName,
      percent_complete: 0,
      todolist_id: this.state.todoList.id,
      key: Math.round(Math.random() * 1000),
      created_at: new Date().toISOString().substring(0, 19).replace('T', ' ')
    };
    let oldTodos = this.state.todos;

    this.setState({ todos: [...(oldTodos || []), todo] });

    this.api.addTodo(this.state.todoList.id, todo).then(persistedTodo => {
      this.setState({ todos: [...(oldTodos || []), persistedTodo]});
    });
  }

  // You should be ashamed of yourself... even for a hackathon.
  updateTodo(updatedTodo) {
    let oldTodoLists = {
      todos: this.state.todos,
      currentTodos: this.state.currentTodos
    };
    let todoWasInCurrent = !!_.find(oldTodoLists.currentTodos, todo => todo.id == updatedTodo.id);

    _.each(oldTodoLists, (todoList, todoListKey) => {
      var newTodoList = oldTodoLists[todoListKey];

      if (_.find(todoList, todo => todo.id == updatedTodo.id)) {
        // Replace the old instances with the new
        newTodoList = [
          ..._.reject(oldTodoLists[todoListKey], todo => todo.id == updatedTodo.id),
          updatedTodo
        ];
      }

      if (todoListKey == 'currentTodos') {
        if (updatedTodo.is_current && !todoWasInCurrent) {
          // Add to current if not there and it should be
          newTodoList = [...newTodoList, updatedTodo];
        } else if (!updatedTodo.is_current && todoWasInCurrent) {
          // Remove from current if there and it shouldn't be
          newTodoList = _.reject(newTodoList, todo => todo.id == updatedTodo.id);
        }
      }

      this.setState({
        [todoListKey]: newTodoList
      });
    });

    this.api.updateTodo(updatedTodo.todolist_id, updatedTodo.id, updatedTodo).catch(error => {
      alert('error: ' + error);

      _.each(oldTodoLists, (todoList, todoListKey) => {
        this.setState({
          [todoListKey]: todoList
        });
      });
    });
  }

  toggleFullscreen() {
    this.setState({
      isFullscreen: !this.state.isFullscreen
    });
  }

  render() {
    return (
      <div className='big-rocks-app'>
        <header>
          <a href="/">TeamGantt</a>
        </header>
        <div className='content'>
          {!this.state.isFullscreen && (<TodoListSidebar todoLists={this.state.todoLists} currentList={this.state.todoList} onClickList={this.showTodos.bind(this)}/>)}
          {!this.state.isFullscreen && (<TodoList onAddTodo={this.addTodo.bind(this)} onUpdateTodo={this.updateTodo.bind(this)} todoList={this.state.todoList} todos={this.state.todos}/>)}
          <CurrentTodoList isFullscreen={this.state.isFullscreen} onToggleFullscreen={this.toggleFullscreen.bind(this)} currentTodos={this.state.currentTodos} onUpdateTodo={this.updateTodo.bind(this)} />
        </div>
      </div>
    );
  }
};

BigRocksApp.propTypes = {
  currentUser: React.PropTypes.shape({
    user_id: React.PropTypes.number,
    user_key: React.PropTypes.string,
    user_token: React.PropTypes.string
  })
}
