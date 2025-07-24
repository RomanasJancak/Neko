export const APP_URL = import.meta.env.VITE_APP_URL;
//import { APP_URL } from './config.js';
window.ROUTES = {
  WEB: {
      TASK: {
          UPDATE: APP_URL+'/tasks/update',
          DELETE: APP_URL+'/tasks/delete',
          STORE:  APP_URL+'/tasks/store',
          GETINFO:  APP_URL+'/tasks/getTaskInfo/:id',
          SWAP_ORDER: APP_URL+'/tasks/swap_order',
      },
      JOB: {
        VIEW  : APP_URL+'/jobs/view',
        UPDATE: APP_URL+'/jobs/update',
        DELETE: APP_URL+'/jobs/delete', 
        STORE:  APP_URL+'/jobs/store',
        STOREFROMSTRING:  APP_URL+'/jobs/storeFromString',
        FETCH:  APP_URL+'/jobs/fetchJobsPaginate',
        GETINFO: APP_URL+'/jobs/getJobInfo/:id',
        GETJOBTOSTRING: APP_URL+'/jobs/getJobToString/:id',
        COPY:  APP_URL+'/jobs/copy',
        UPDATE_PRICEADJUSTMENTNUMBER:  APP_URL+'/jobs/update_price_adjustment_number',
        CREATE_JOBTEMPLATE_FROMTHISJOB: APP_URL+'/jobs/create_JobTemplate_fromThisJob/:id',
      },
      JOBTEMPLATE: {
        GETINFO: APP_URL+'/jobtemplates/getJobTemplateInfo/:id',
        STORE: APP_URL+'/jobtemplates/store',
        UPDATE: APP_URL+'/jobtemplates/update',
        DELETE: APP_URL+'/jobtemplates/delete',
        FETCH: APP_URL+'/jobtemplates/fetchJobTemplatesPaginate',
      },
      CLIENT:{
        SEARCHADDRESSES:  APP_URL+'/clients/searchClientAddresses',
        GETINFO: APP_URL+'/clients/getClientInfo/:id',
        SEARCH: APP_URL+'/clients/searchClients',
        FETCHPACKAGETYPES: APP_URL+'/clients/fetchPackageTypes/:id',
        FETCHADDONS: APP_URL+'/clients/fetchAddOns/:id',
        FETCHUNASSIGNEDPACKAGETYPES: APP_URL+'/clients/fetchUnassignedPackageTypes/:id',
        ADDPACKAGETYPE: APP_URL+'/clients/addPackageType',
        REMOVEPACKAGETYPE: APP_URL+'/clients/removePackageType',
        UPDATEDISTANCERULES: APP_URL+'/clients/updateDistanceRules',
        UPDATEWEIGHTRULES: APP_URL+'/clients/updateWeightRules',
      },
      ADDRESS:{
        GETINFO:  APP_URL+'/addresses/getAddressInfo/:id',
      },
      PACKAGETYPE:{
        GETINFO: APP_URL+'/packageTypes/getPackageTypeInfo/:id',
      },
  }
};