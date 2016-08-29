import React from 'react';
import Todo from './todo';
require('../styles/modules/todo-lists');
require('../styles/modules/todos');

let enterKeyCode = 13;

export default class TodoList extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      newTodoName: ''
    };
  }

  onChange(e) {
    this.setState({newTodoName: e.target.value});
  }

  onKeyDown(e) {
    if (e.keyCode == enterKeyCode) {
      this.props.onAddTodo(this.state.newTodoName);
      this.setState({
        newTodoName: ''
      });
    }
  }

  render() {
    if (!this.props.todoList) {
      return (<div/>);
    }

    return (
      <div className='todo-list'>
        <div className='todo-list-scroll'>
          <h1 className='todo-list-name'>{this.props.todoList.name}</h1>
          <div className='todo-list-inner'>
            <ul>
              {_(this.props.todos).sortBy(['percentComplete', 'createdAt']).map(todo => {
                return (<Todo todo={todo} key={todo.id || todo.key} onUpdateTodo={this.props.onUpdateTodo} />);
              }).value()}
            </ul>
          </div>
        </div>
        <input className='add-task' type='text' value={this.state.newTodoName} onChange={this.onChange.bind(this)} onKeyDown={this.onKeyDown.bind(this)} placeholder='Add Task'/>
      </div>
    );
  }
};

TodoList.propTypes = {
  todos: React.PropTypes.arrayOf(React.PropTypes.shape({id: React.PropTypes.number, name: React.PropTypes.string})),
  todoList: React.PropTypes.shape({id: React.PropTypes.number, name: React.PropTypes.string}),
  onAddTodo: React.PropTypes.func,
  onUpdateTodo: React.PropTypes.func
};
