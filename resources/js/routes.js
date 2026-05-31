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
          UPDATE_STATUS: APP_URL+'/tasks/updateStatus',
      },
      NOTE: {
          GETINFO: APP_URL+'/notes/getNoteInfo/:id',
      },
      JOB: {
        VIEW  : APP_URL+'/jobs/view',
        UPDATE: APP_URL+'/jobs/update',
        DELETE: APP_URL+'/jobs/delete', 
        STORE:  APP_URL+'/jobs/store',
        STOREFROMSTRING:  APP_URL+'/jobs/storeFromString',
        STOREFROMTEMPLATE:  APP_URL+'/jobs/storeFromTemplate',
        RESTORE_NOTE_FROM_TEMPLATE: APP_URL+'/jobs/restoreNoteFromTemplate',
        FETCH:  APP_URL+'/jobs/fetchJobsPaginate',
        FETCHLIGHT:  APP_URL+'/jobs/fetchJobsPaginateLight',
        GETINFO: APP_URL+'/jobs/getJobInfo/:id',
        GETJOBTOSTRING: APP_URL+'/jobs/getJobToString/:id',
        COPY:  APP_URL+'/jobs/copy',
        UPDATE_PRICEADJUSTMENTNUMBER:  APP_URL+'/jobs/update_price_adjustment_number',
        CREATE_JOBTEMPLATE_FROMTHISJOB: APP_URL+'/jobs/create_JobTemplate_fromThisJob/:id',
      },
      JOBTEMPLATE: {
        GETINFO: APP_URL+'/jobtemplates/:id/info',
        STORE: APP_URL+'/jobtemplates',
        UPDATE: APP_URL+'/jobtemplates/:id',
        DELETE: APP_URL+'/jobtemplates/:id',
        FETCH: APP_URL+'/jobtemplates/fetch', 
        CREATE_FROM_JOB: APP_URL+'/jobtemplates/createFromJob',
        CREATE_JOBS: APP_URL+'/jobtemplates/createJobsBatch',
        SET_FIELD_LOCK: APP_URL+'/jobtemplates/:id/setFieldLock',
        ADD_DROPOFF: APP_URL+'/jobtemplates/:id/addDropOff',
        REMOVE_DROPOFF: APP_URL+'/jobtemplates/:id/removeDropOff',
        ADD_RETURN: APP_URL+'/jobtemplates/:id/addReturn',
        REMOVE_RETURN: APP_URL+'/jobtemplates/:id/removeReturn',
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
      EMAIL: {
        DELETE: APP_URL+'/emails/delete/:id',
      },
      PACKAGETYPE:{
        GETINFO: APP_URL+'/packageTypes/getPackageTypeInfo/:id',
      },
      COURIER: {
        GET_COURIERS_FOR_DATE: APP_URL+'/courier/getCouriersForDate/:date',
      },
  }
};