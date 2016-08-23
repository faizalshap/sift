import reqwest from 'reqwest';

function apiReq(path) {
  return reqwest({
    url: `${process.env.API_URL}/${path}`,
    headers: {
      'BR-Api-Key': 'dev',
      'BR-User-Token': 'dev'
    }
  });
}

export default {
  getTodoLists() {
    return apiReq('todolists');
  },

  getTodos(todoListId) {
    return apiReq(`todolists/${todoListId}/todos`);
  }
};
