import reqwest from 'reqwest';

function apiReq(path, options) {
  let reqOptions = {
    ...options,
    url: `${process.env.API_URL}/${path}`,
    headers: {
      'BR-Api-Key': 'dev',
      'BR-User-Token': 'dev'
    }
  };

  return reqwest(reqOptions);
}

export default {
  getTodoLists() {
    return apiReq('todolists');
  },

  getTodos(todoListId) {
    return apiReq(`todolists/${todoListId}/todos`);
  },

  addTodo(todoListId, todo) {
    return apiReq(`todolists/${todoListId}/todos`, {
      method: 'post',
      data: JSON.stringify(todo)
    });
  }
};
