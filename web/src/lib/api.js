import reqwest from 'reqwest';

export default class Api {
  constructor(user) {
    this.user = user;
  }

  apiReq(path, options) {
    let reqOptions = {
      url: `${process.env.API_URL}/${path}`,
      headers: {
        'S-Api-Key': this.user.user_key,
        'S-User-Token': this.user.user_token
      },
      ...options
    };

    return reqwest(reqOptions);
  }

  signIn(attrs) {
    return this.apiReq('login', {
      method: 'post',
      headers: {},
      data: JSON.stringify(attrs)
    });
  }

  getTodoLists() {
    return this.apiReq('todolists');
  }

  getTodos(todoListId) {
    return this.apiReq(`todolists/${todoListId}/todos`);
  }

  getCurrentTodos() {
    return this.apiReq('current');
  }

  addTodo(todoListId, todo) {
    return this.apiReq(`todolists/${todoListId}/todos`, {
      method: 'post',
      data: JSON.stringify(todo)
    });
  }

  updateTodo(todoListId, todoId, updatedTodo) {
    return this.apiReq(`todolists/${todoListId}/todos/${todoId}`, {
      method: 'put',
      data: JSON.stringify(updatedTodo)
    });
  }
};
