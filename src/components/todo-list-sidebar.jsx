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

  onDestroyTodoList(todoList, e) {
    const confirmDestroy = confirm(`Are you sure you want to delete "${todoList.name}" AND all of its tasks?  This can't be undone.`);
    e.stopPropagation();

    if (confirmDestroy) {
      this.props.onDestroyTodoList(todoList);
    }
  }

  render() {
    if (!this.props.todoLists) { return (<div />); }

    const inbox = _.find(this.props.todoLists, todoList => todoList.name == 'Inbox');
    const teamganttTodoList = _.find(this.props.todoLists, todoList => todoList.id == 'teamgantt');
    const otherTodoLists = _.without(this.props.todoLists, inbox, teamganttTodoList);

    return (
      <div className={`todo-list-sidebar ${this.props.todoListsAreLoading ? 'loading' : ''}`}>
        <h1>Lists</h1>
        <ul className='todo-list-links'>
          <li className={`todo-list-link inbox ${inbox == this.props.currentList && 'current'}`} onClick={_.partial(this.props.onClickList, inbox)} key={inbox.id}>
            <div className='name'>Inbox</div>
          </li>
          <li className={`todo-list-link teamgantt ${teamganttTodoList == this.props.currentList && 'current'}`} onClick={_.partial(this.props.onClickList, teamganttTodoList)} key={teamganttTodoList.id}>
            <div className='name'>{teamganttTodoList.name}</div>
          </li>
          {_(otherTodoLists).sortBy('createdAt').map(todoList => {
            return (
              <li className={`todo-list-link ${todoList == this.props.currentList && 'current'}`} onClick={_.partial(this.props.onClickList, todoList)} key={todoList.id}>
                <div className='name'>{todoList.name}</div>
                <button onClick={_.partial(this.onDestroyTodoList.bind(this), todoList)} className='link destroy'>×</button>
              </li>
            );
          }).value()}
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
  onDestroyTodoList: React.PropTypes.func,
  currentList: React.PropTypes.shape({
    id: React.PropTypes.number,
    name: React.PropTypes.string
  })
};
