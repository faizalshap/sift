import React from 'react';
require('../styles/modules/sidebar');

let enterKeyCode = 13;

export default class TodoListSidebar extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      newTodoListName: ''
    };
  }

  onChange(e) {
    this.setState({newTodoListName: e.target.value});
  }

  onKeyDown(e) {
    if (e.keyCode == enterKeyCode) {
      this.props.onAddTodoList(this.state.newTodoListName);
      this.setState({
        newTodoListName: ''
      });
    }
  }

  render() {
    if (!this.props.todoLists) { return (<div />); }

    return (
      <div className={`todo-list-sidebar ${this.props.todoListsAreLoading ? 'loading' : ''}`}>

        <h1>Lists</h1>
        <ul className='todo-list-links'>
          {_.map(this.props.todoLists, todoList => {
            return (
              <li className={`todo-list-link ${todoList == this.props.currentList && 'current'}`} onClick={_.partial(this.props.onClickList, todoList)} key={todoList.id}>{todoList.name}</li>
            );
          })}
        </ul>
        <input type='text' disabled={this.props.todoListsAreLoading} className='add-todo-list' value={this.state.newTodoListName} onChange={this.onChange.bind(this)} onKeyDown={this.onKeyDown.bind(this)} placeholder='Add List' />
      </div>
    );
  }
};

TodoListSidebar.propTypes = {
  todoLists: React.PropTypes.arrayOf(React.PropTypes.shape({
    id: React.PropTypes.number,
    name: React.PropTypes.string
  })),
  todoListsAreLoading: React.PropTypes.bool,
  onClickList: React.PropTypes.func,
  onAddTodoList: React.PropTypes.func,
  currentList: React.PropTypes.shape({
    id: React.PropTypes.number,
    name: React.PropTypes.string
  })
};
