import React from 'react';
import _ from 'lodash';

export default class Signin extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      username: '',
      password: ''
    };
  }

  onChange(field, e) {
    this.setState({ [field]: e.target.value });
  }

  onSubmit(e) {
    e.preventDefault();

    this.props.onSubmit({
      username: this.state.username,
      password: this.state.password
    });
  }

  render() {
    return (
      <div className='signin'>
        {this.props.error}
        <form onSubmit={this.onSubmit.bind(this)}>
          <input type='text' value={this.state.username} onChange={_.partial(this.onChange.bind(this), 'username')} name='username' placeholder='Username' />
          <input type='password' value={this.state.password} onChange={_.partial(this.onChange.bind(this), 'password')} name='password' placeholder='Password' />
          <input type='submit' name='Sign In' />
        </form>
      </div>
    );
  }
}

Signin.propTypes = {
  onSubmit: React.PropTypes.func,
  error: React.PropTypes.string
}
