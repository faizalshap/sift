import React from 'react';

export default class CurrentTodoList extends React.Component {
  onCheck(todo) {
    let updatedTodo = {
      ...todo,
      percent_complete: (todo.percent_complete == 100 ? 0 : 100)
    };

    this.props.onUpdateTodo(updatedTodo);
  }

  bigRocks() {
    return _.filter(this.props.currentTodos, todo => todo.is_big_rock);
  }

  otherCurrentTodos() {
    return _.reject(this.props.currentTodos, todo => todo.is_big_rock);
  }

  onToggleCurrent(todo) {
    let updatedTodo = {
      ...todo,
      is_current: !todo.is_current
    };

    this.props.onUpdateTodo(updatedTodo);
  }

  render() {
    if (!this.props.currentTodos) { return (<div />); }

    return (
      <div className='current-todos'>
        <h1 className='big-rocks-header'>Big Rocks</h1>
        <ul>
          {_.map(this.bigRocks(), todo => {
             return (
               <li key={todo.id || todo.key}>
                 <div className={`checkbox ${todo.percent_complete == 100 && 'checked'}`} onClick={_.partial(this.onCheck.bind(this), todo)}>
                   {todo.percent_complete == 100 ? 'checked' : 'unchecked'}
                 </div>
                 {todo.name}
                 <button onClick={_.partial(this.onToggleCurrent.bind(this), todo)} className={`current-button ${todo.is_current && 'active'}`}>
                   {todo.is_current && '*'}
                   Current
                 </button>
               </li>
             );
           })}
        </ul>

        <h2 className='other-current-header'>Other</h2>
        <ul>
          {_.map(this.otherCurrentTodos(), todo => {
             return (
               <li key={todo.id || todo.key}>
                 <div className={`checkbox ${todo.percent_complete == 100 && 'checked'}`} onClick={_.partial(this.onCheck.bind(this), todo)}>
                   {todo.percent_complete == 100 ? 'checked' : 'unchecked'}
                 </div>
                 {todo.name}
                 <button onClick={_.partial(this.onToggleCurrent.bind(this), todo)} className={`current-button ${todo.is_current && 'active'}`}>
                   {todo.is_current && '*'}
                   Current
                 </button>
               </li>
             );
           })}
        </ul>
      </div>
    );
  }
};

CurrentTodoList.propTypes = {
  currentTodos: React.PropTypes.arrayOf(React.PropTypes.shape({
    id: React.PropTypes.number,
    name: React.PropTypes.string
  })),
  onUpdateTodo: React.PropTypes.func
};
