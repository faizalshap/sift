import React from 'react';

export default class TaskList extends React.Component {
  render() {
    if (!this.props.todos) { return (<div />); }

    return (
      <div className='task-list'>
        <ul>
          {_.map(this.props.todos, todo => (<li key={todo.id}>{todo.name}</li>))}
        </ul>

        <input type='text' placeholder='Add a todo' />
      </div>
    );
  }
};

TaskList.propTypes = {
  todos: React.PropTypes.arrayOf(React.PropTypes.shape({
    id: React.PropTypes.number,
    name: React.PropTypes.string
  }))
};
