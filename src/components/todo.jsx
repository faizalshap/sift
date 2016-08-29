import React from 'react';

export default class Todo extends React.Component {
  onCheck() {
    const { todo } = this.props;
    let updatedTodo = {
      ...todo,
      percentComplete: (todo.percentComplete == 100 ? 0 : 100)
    };

    this.props.onUpdateTodo(updatedTodo);
  }

  onToggleCurrent() {
    const { todo } = this.props;
    let updatedTodo = {
      ...todo,
      isCurrent: !todo.isCurrent
    };

    this.props.onUpdateTodo(updatedTodo);
  }

  onToggleBigRock() {
    const { todo } = this.props;
    let isNowBigRock = !todo.isBigRock;

    let updatedTodo = {
      ...todo,
      isCurrent: (isNowBigRock ? true : todo.isCurrent),
      isBigRock: isNowBigRock
    };

    this.props.onUpdateTodo(updatedTodo);
  }

  render() {
    const { todo } = this.props;

    return (
      <li className={`todo ${todo.percentComplete == 100 && 'checked'}`} onDoubleClick={this.onToggleBigRock.bind(this)}>
        <div className='checkbox-col'>
          <div className={`checkbox ${todo.percentComplete == 100 && 'checked'}`} onClick={this.onCheck.bind(this)} />
        </div>
        <div className='todo-name-col'>
          {todo.name}
        </div>
        <div className='current-button-col'>
          <button onClick={this.onToggleCurrent.bind(this)} className={`current-button ${todo.isCurrent && 'active'}`} />
        </div>
      </li>
    );
  }
}

Todo.propTypes = {
  todo: React.PropTypes.shape({
    id: React.PropTypes.number,
    isBigRock: React.PropTypes.bool,
    isCurrent: React.PropTypes.bool,
    key: React.PropTypes.number,
    name: React.PropTypes.string,
    percentComplete: React.PropTypes.number
  }),
  onUpdateTodo: React.PropTypes.func
}
