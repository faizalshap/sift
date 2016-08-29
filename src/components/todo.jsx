import React from 'react';

export default class Todo extends React.Component {
  onCheck() {
    const { todo } = this.props;
    let updatedTodo = {
      ...todo,
      percent_complete: (todo.percent_complete == 100 ? 0 : 100)
    };

    this.props.onUpdateTodo(updatedTodo);
  }

  onToggleCurrent() {
    const { todo } = this.props;
    let updatedTodo = {
      ...todo,
      is_current: !todo.is_current
    };

    this.props.onUpdateTodo(updatedTodo);
  }

  onToggleBigRock() {
    const { todo } = this.props;
    let isNowBigRock = !todo.is_big_rock;

    let updatedTodo = {
      ...todo,
      is_current: (isNowBigRock ? true : todo.is_current),
      is_big_rock: isNowBigRock
    };

    this.props.onUpdateTodo(updatedTodo);
  }

  render() {
    const { todo } = this.props;

    return (
      <li className={`todo ${todo.percent_complete == 100 && 'checked'}`} onDoubleClick={this.onToggleBigRock.bind(this)}>
        <div className='checkbox-col'>
          <div className={`checkbox ${todo.percent_complete == 100 && 'checked'}`} onClick={this.onCheck.bind(this)} />
        </div>
        <div className='todo-name-col'>
          {todo.name}
        </div>
        <div className='current-button-col'>
          <button onClick={this.onToggleCurrent.bind(this)} className={`current-button ${todo.is_current && 'active'}`} />
        </div>
      </li>
    );
  }
}

Todo.propTypes = {
  todo: React.PropTypes.shape({
    id: React.PropTypes.number,
    is_current: React.PropTypes.bool,
    key: React.PropTypes.number,
    name: React.PropTypes.string,
    percent_complete: React.PropTypes.number
  }),
  onUpdateTodo: React.PropTypes.func
}
