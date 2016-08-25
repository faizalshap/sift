import React from 'react';
require('../styles/modules/current-todos.css');

export default class CurrentTodoList extends React.Component {
  onCheck(todo) {
    let updatedTodo = {
      ...todo,
      percent_complete: (todo.percent_complete == 100 ? 0 : 100)
    };

    this.props.onUpdateTodo(updatedTodo);
  }

  bigRocks() {
    return _(this.props.currentTodos)
      .filter(todo => todo.is_big_rock)
      .sortBy(['percent_complete', 'created_at'])
      .value();
  }

  otherCurrentTodos() {
    return _(this.props.currentTodos)
      .reject(todo => todo.is_big_rock)
      .sortBy(['percent_complete', 'created_at'])
      .value();
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
    if (!this.props.currentTodos) { return (<div />); }

    return (
      <div className='current-todos'>
        <h1 className='big-rocks-header'>Top Sifted Items</h1>

        {!this.bigRocks().length && (
           <div className='no-rocks'>
             <i className='sifted-icon'/>
             Sift through your list and put your highest priority tasks here
             <div className='sub'>Double click items to bring them here</div>
           </div>
        )}

        <ul className='big-rocks'>
          {_.map(this.bigRocks(), todo => {
             return (
               <li className={`todo ${todo.percent_complete == 100 && 'checked'}`} key={todo.id || todo.key} onDoubleClick={_.partial(this.onToggleBigRock.bind(this), todo)}>
                 <div className={`checkbox ${todo.percent_complete == 100 && 'checked'}`} onClick={_.partial(this.onCheck.bind(this), todo)}>
                 </div>
                 {todo.name}
                 <button onClick={_.partial(this.onToggleCurrent.bind(this), todo)} className={`current-button ${todo.is_current && 'active'}`}>
                 </button>
               </li>
             );
           })}
        </ul>

        <h2 className='other-current-header'>Other</h2>
        <ul className='other-rocks'>
          {_.map(this.otherCurrentTodos(), todo => {
             return (
               <li className={`todo ${todo.percent_complete == 100 && 'checked'}`} key={todo.id || todo.key} onDoubleClick={_.partial(this.onToggleBigRock.bind(this), todo)}>
                 <div className={`checkbox ${todo.percent_complete == 100 && 'checked'}`} onClick={_.partial(this.onCheck.bind(this), todo)}>
                 </div>
                 {todo.name}
                 <button onClick={_.partial(this.onToggleCurrent.bind(this), todo)} className={`current-button ${todo.is_current && 'active'}`}>
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
