// /src/plugins/axios.js
import Vue from 'vue';
import axios from 'axios';

axios.interceptors.request.use(
  (request) => {
    // eslint-disable-next-line no-param-reassign
    request.config = {
      ...(request.config ?? {}), // preserve a given request.config object
      start: Date.now(),
    };

    return request;
  },
);

axios.interceptors.response.use(
  (response) => {
    const now = Date.now();
    console.info(`Api Call ${response.config.url} took ${now - response.config.config.start}ms`);
    return response;
  },
  (error) => { 
    if(error.response.data.exception = "Illuminate\\Database\\QueryException"){
      if(error.response.data.message.startsWith('SQLSTATE[23000]')){
        console.log('FALTA COMPLETAR ALGO')
      }
    }
    //if(error.)
    let path = '/error';
    switch (error.response.status) {
        case 401: path = '/adm/login'; break; // unauthorized
        case 404: path = '/adm/#/404'; break; // not found
        case 403: path = '/adm/#/403'; break; // not privileges for section
        case 500: path = '/adm/#/500'; break; // not privileges for section
    }
    //Vue.$router.push(path);
    window.location = path
  }
);

export default axios;