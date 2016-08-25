import React from 'react';
require('../styles/modules/sidebar');

export default class TodoListSidebar extends React.Component {
  render() {
    if (!this.props.todoLists) { return (<div />); }

    return (
      <div className='todo-list-sidebar'>

        <h1>Lists</h1>
        {/* <div className='add-item'></div> */}
        <ul>
          {_.map(this.props.todoLists, todoList => (<li onClick={() => this.props.onClickList(todoList)} key={todoList.id}>{todoList.name}</li>))}
        </ul>
      </div>
    );
  }
};

TodoListSidebar.propTypes = {
  todoLists: React.PropTypes.arrayOf(React.PropTypes.shape({
    id: React.PropTypes.number,
    name: React.PropTypes.string
  })),
  onClickList: React.PropTypes.func
};
