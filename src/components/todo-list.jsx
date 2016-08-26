import React from 'react';
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

  onCheck(todo) {
    let updatedTodo = {
      ...todo,
      percent_complete: (todo.percent_complete == 100
        ? 0
        : 100)
    };

    this.props.onUpdateTodo(updatedTodo);
  }

  onToggleCurrent(todo) {
    let updatedTodo = {
      ...todo,
      is_current: !todo.is_current
    };

    this.props.onUpdateTodo(updatedTodo);
  }

  onToggleBigRock(todo) {
    let isNowBigRock = !todo.is_big_rock;

    let updatedTodo = {
      ...todo,
      is_current: (isNowBigRock ? true : todo.is_current),
      is_big_rock: isNowBigRock
    };

    this.props.onUpdateTodo(updatedTodo);
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
              {_(this.props.todos).sortBy(['percent_complete', 'created_at']).map(todo => {
                return (
                  <li className={`todo ${todo.percent_complete == 100 && 'checked'}`} key={todo.id || todo.key} onDoubleClick={_.partial(this.onToggleBigRock.bind(this), todo)}>
                    <div className='checkbox-col'>
                      <div className={`checkbox ${todo.percent_complete == 100 && 'checked'}`} onClick={_.partial(this.onCheck.bind(this), todo)} />
                    </div>
                    <div className='todo-name-col'>
                      {todo.name}
                    </div>
                    <div className='current-button-col'>
                      <button onClick={_.partial(this.onToggleCurrent.bind(this), todo)} className={`current-button ${todo.is_current && 'active'}`} />
                    </div>
                  </li>
                );
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
