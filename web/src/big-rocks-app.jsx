import React from 'react';
import _ from 'lodash';
import TodoListSidebar from './components/todo-list-sidebar';
import TodoList from './components/todo-list';
import api from './lib/api';

export default class BigRocksApp extends React.Component {
  constructor(props) {
    super(props);
    this.state = {};
  }

  componentDidMount() {
    api.getTodoLists().then(todoLists => {
      this.setState({ todoLists: todoLists });
      this.showTasks(todoLists[0].id);
    });
  }

  showTasks(todoListId) {
    this.setState({ todos: undefined })
    api.getTodos(todoListId).then(todos => this.setState({ todos: todos }));
  }

  render() {
    return (
      <div className='big-rocks-app'>
        <TodoListSidebar todoLists={this.state.todoLists} onClickList={this.showTasks.bind(this)} />
        <TodoList todos={this.state.todos} />
      </div>
    );
  }
};
