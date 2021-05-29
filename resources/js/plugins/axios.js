// /src/plugins/axios.js
import Vue from 'vue';
import axios from 'axios';
import EventBus from '../service/event-bus';

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
    //EventBus.$emit('axiosSuccess', response);
    return response;
  },
  (error) => { 
    
    console.log('error', error)
    let path = '/adm/#/error';


    switch (error.response.status) {
        case 401: path = '/adm/login'; window.location = path; break; // unauthorized
        case 404: path = '/adm/#/404'; window.location = path; break; // not found
        case 403: path = '/adm/#/403'; window.location = path; break; // not privileges for section
        case 422: path = '/adm/#/422'; window.location = path; break; // error in fields
        //case 500: path = '/adm/#/500'; window.location = path; break; // server error
    }
    //Vue.$router.push(path);
    if(path = '/adm/#/422'){
        let msg = error.response.data
        EventBus.$emit('axiosError', msg);
    }

  }
);

export default axios;