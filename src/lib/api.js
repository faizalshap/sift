import reqwest from 'reqwest';
import { toCamelCase, toSnakeCase } from 'case-converter';

export default class Api {
  constructor(user) {
    this.user = user;
  }

  apiReq(path, options) {
    let reqOptions = {
      url: `${process.env.API_URL}/${path}`,
      headers: {
        'S-Api-Key': this.user.userKey,
        'S-User-Token': this.user.userToken
      },
      ...options
    };

    if (options && options.data) {
      reqOptions.data = JSON.stringify(toSnakeCase(options.data));
    }

    return reqwest(reqOptions).then(toCamelCase);
  }

  signIn(attrs) {
    return this.apiReq('login', {
      method: 'post',
      headers: {},
      data: attrs
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
      data: todo
    });
  }

  destroyTodo(todoListId, todoId) {
    return this.apiReq(`todolists/${todoListId}/todos/${todoId}`, {
      method: 'delete'
    });
  }

  updateTodo(todoListId, todoId, updatedTodo) {
    return this.apiReq(`todolists/${todoListId}/todos/${todoId}`, {
      method: 'put',
      data: updatedTodo
    });
  }

  addTodoList(todoList) {
    return this.apiReq(`todolists`, {
      method: 'post',
      data: todoList
    });
  }

  destroyTodoList(todoListId) {
    return this.apiReq(`todolists/${todoListId}`, {
      method: 'delete'
    });
  }
};
