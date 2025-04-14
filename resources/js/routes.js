window.ROUTES = {
  WEB: {
      TASK: {
          UPDATE: window.location.href.split('/').slice(0, -1).join('/')+'/tasks/update',
          DELETE: window.location.href.split('/').slice(0, -1).join('/')+'/tasks/delete',
          STORE:  window.location.href.split('/').slice(0, -1).join('/')+'/tasks/store',
          GETINFO:  window.location.href.split('/').slice(0, -1).join('/')+'/tasks/getTaskInfo/:id',
          SWAP_ORDER: window.location.href.split('/').slice(0, -1).join('/')+'/tasks/swap_order',
      },
      JOB: {
        VIEW  : window.location.href.split('/').slice(0, -1).join('/')+'/jobs/view',
        UPDATE: window.location.href.split('/').slice(0, -1).join('/')+'/jobs/update',
        DELETE: window.location.href.split('/').slice(0, -1).join('/')+'/jobs/delete',
        STORE:  window.location.href.split('/').slice(0, -1).join('/')+'/jobs/store',
        FETCH:  window.location.href.split('/').slice(0, -1).join('/')+'/jobs/fetchJobsPaginate',
        GETINFO: window.location.href.split('/').slice(0, -1).join('/')+'/jobs/getJobInfo/:id',
        COPY:  window.location.href.split('/').slice(0, -1).join('/')+'/jobs/copy',
        UPDATE_PRICEADJUSTMENTNUMBER:  window.location.href.split('/').slice(0, -1).join('/')+'/jobs/update_price_adjustment_number',
      },
      CLIENT:{
        SEARCHADDRESSES:  window.location.href.split('/').slice(0, -1).join('/')+'/clients/searchClientAddresses',
        GETINFO: window.location.href.split('/').slice(0, -1).join('/')+'/get-client-info/:id',
        SEARCH: window.location.href.split('/').slice(0, -1).join('/')+'/clients/searchClients',
        FETCHPACKAGETYPES: window.location.href.split('/').slice(0, -1).join('/')+'/clients/fetchPackageTypes/:id',
        FETCHADDONS: window.location.href.split('/').slice(0, -1).join('/')+'/clients/fetchAddOns/:id',
        FETCHUNASSIGNEDPACKAGETYPES: window.location.href.split('/').slice(0, -1).join('/')+'/clients/fetchUnassignedPackageTypes/:id',
        ADDPACKAGETYPE: window.location.href.split('/').slice(0, -1).join('/')+'/clients/addPackageType',
        REMOVEPACKAGETYPE: window.location.href.split('/').slice(0, -1).join('/')+'/clients/removePackageType',
        UPDATEDISTANCERULES: window.location.href.split('/').slice(0, -1).join('/')+'/clients/updateDistanceRules',
      },
      ADDRESS:{
        GETINFO:  window.location.href.split('/').slice(0, -1).join('/')+'/addresses/getAddressInfo/:id',
      },
      PACKAGETYPE:{
        GETINFO: window.location.href.split('/').slice(0, -1).join('/')+'/packageTypes/getPackageTypeInfo/:id',
      },
  }
};