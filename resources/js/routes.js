window.ROUTES = {
  WEB: {
      TASK: {
          UPDATE: window.location.href.split('/').slice(0, -1).join('/')+'/tasks/update',
          DELETE: window.location.href.split('/').slice(0, -1).join('/')+'/tasks/delete',
          STORE:  window.location.href.split('/').slice(0, -1).join('/')+'/tasks/store',
      },
      JOB: {
        VIEW  : window.location.href.split('/').slice(0, -1).join('/')+'/jobs/view',
        UPDATE: window.location.href.split('/').slice(0, -1).join('/')+'/jobs/update',
        DELETE: window.location.href.split('/').slice(0, -1).join('/')+'/jobs/delete',
        STORE:  window.location.href.split('/').slice(0, -1).join('/')+'/jobs/store',
        FETCH:  window.location.href.split('/').slice(0, -1).join('/')+'/jobs/fetchJobsPaginate',
      },
      CLIENT:{
        SEARCHADDRESSES:  window.location.href.split('/').slice(0, -1).join('/')+'/clients/searchClientAddresses',
      },
      ADDRESS:{
        GETINFO:  window.location.href.split('/').slice(0, -1).join('/')+'/address/getAddressInfo/:addressId',
      },
  }
};