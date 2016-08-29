import React from 'react';
import Todo from './todo';
require('../styles/modules/current-todos');
require('../styles/modules/icons');

export default class CurrentTodoList extends React.Component {
  bigRocks() {
    return _(this.props.currentTodos)
      .filter(todo => todo.isBigRock)
      .sortBy(['percentComplete', 'createdAt'])
      .value();
  }

  otherCurrentTodos() {
    return _(this.props.currentTodos)
      .reject(todo => todo.isBigRock)
      .sortBy(['percentComplete', 'createdAt'])
      .value();
  }

  render() {
    if (!this.props.currentTodos) { return (<div />); }

    return (
      <div className={`current-todos ${this.props.isFullscreen ? 'fullscreen' : ''}`}>
        <div className='current-todos-inner'>
          <i onClick={this.props.onToggleFullscreen} className='expand-icon'/>
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
              return (<Todo todo={todo} key={todo.id || todo.key} onUpdateTodo={this.props.onUpdateTodo} />);
            })}
          </ul>

          <h2 className='other-current-header'>Other</h2>
          <ul className='other-rocks'>
            {_.map(this.otherCurrentTodos(), todo => {
              return (<Todo todo={todo} key={todo.id || todo.key} onUpdateTodo={this.props.onUpdateTodo} />);
            })}
          </ul>
        </div>
      </div>
    );
  }
};

CurrentTodoList.propTypes = {
  currentTodos: React.PropTypes.arrayOf(React.PropTypes.shape({
    id: React.PropTypes.number,
    name: React.PropTypes.string
  })),
  onUpdateTodo: React.PropTypes.func,
  onToggleFullscreen: React.PropTypes.func,
  isFullscreen: React.PropTypes.bool
};
