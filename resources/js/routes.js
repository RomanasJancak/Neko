window.ROUTES = {
  WEB: {
      TASK: {
          UPDATE: window.location.href.split('/').slice(0, -1).join('/')+'/tasks/update',
          DELETE: window.location.href.split('/').slice(0, -1).join('/')+'/tasks/delete',
          STORE:  window.location.href.split('/').slice(0, -1).join('/')+'/tasks/store',
      },
      JOB: {
          UPDATE: window.location.href.split('/').slice(0, -1).join('/')+'/jobs/update',
          DELETE: window.location.href.split('/').slice(0, -1).join('/')+'/jobs/delete',
          STORE:  window.location.href.split('/').slice(0, -1).join('/')+'/jobs/store',
      }
  }
};