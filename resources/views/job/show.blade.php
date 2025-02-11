<div class="modal fade" id="jobModalWindow" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="row g-0">
                <div class="col-9 border rounded d-flex flex-column "> <!--Left side -->
                    <div class="modal-body">
                        <form id="jobForm" action="" method="POST">
                            @csrf
                            <div class="row justify-content-md-center">
                                <div class="col-2">
                                    <div class="row">
                                        <input type="hidden" name="jobid" id="jobid" value="">
                                        <label for="idField">Id</label>
                                        <input class="form-control" type="text" name="id" id="idField" value="">
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="row">
                                        <label for="courierIdField">Courier</label>
                                        <select id="courierIdField" name="courierId" class="form-control" >
                                            <option value="0">none</option>
                                            @foreach($couriers as $courier)
                                            <option value="{{ $courier->id }}">{{ $courier->name }}</option>
                                            @endforeach                                                                              
                                        </select>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="row">
                                        <label for="statusIdField">Status</label>
                                        <select id="statusIdField" name="statusId" class="form-control">
                                        @foreach($statuses as $status)
                                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                                        @endforeach 
                                    </select>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="row">
                                        <label for="clientSearchField">Client</label>
                                        <input type="text" id="clientSearchField" name="clientName" class="form-control" placeholder="Search for clients">
                                        <input type="hidden" name="clientId" id="clientIdField" value="">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="row">
                                        <label for="jobDateField">Date</label>
                                        <input type="date" id="jobDateField" name="jobDate" class="form-control" placeholder="Search for clients">
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-md-center">
                                Tasks
                            </div>
                            <div class="row justify-content-md-center border rounded border-info" >
                                <div class="col" id="container-tasks">
                                </div>                        
                            </div>
                            <div class="row justify-content-md-center">
                                <div class="col">
                                    <button type="button" id="createNewTask" data-option="create" class="btn btn-primary ">Create new Task</button>
                                    <button type="button" id="createNewPickup"data-type="specifictask" data-option="pickup" class="btn btn-primary">+Pickup</button>
                                    <button type="button" id="createNewDropOff" data-type="specifictask" data-option="return" class="btn btn-primary">+DropOff</button>
                                    <button type="button" id="createNewReturn" data-type="specifictask" data-option="dropOff" class="btn btn-primary">+Return</button>                                    
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer ">
                        <!-- <div class="row"> -->
                            <div class="col-12">
                                <div class="form-group d-flex justify-content-between">
                                    <button type="button" id="submitform" data-option="create" class="btn btn-success">Confirm</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="jobModalWindowCloseButton">Cancel</button>
                                </div>
                            </div>
                            <!-- <div class="col-auto"></div> -->
                        <!-- </div>                 -->
                    </div>
                </div>
                <div class="col-3 border rounded justify-content-left"> <!--Right side -->
                    <div class="row">
                        <div class="col-12">
                            <span style="font-size: larger; font-weight: bold;">Total price : </span><span style="font-size: larger; font-weight: bold;">&#163;</span><span id="total_Price_DisplayField" style="font-size: larger; font-weight: bold;">0.00</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <span>Total distance : </span><span id="total_distance_DisplayField">0.00</span><span> miles</span>
                        </div>
                        <div class="col-12">
                            <span>Price from distance : </span><span>&#163;</span><span id="total_distance_price_DisplayField">0.00</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <span>Total weight : </span><span id="total_weight_DisplayField">0.00</span><span>kg</span>
                        </div>
                        <div class="col-12">
                            <span>Price from weight : </span><span>&#163;</span><span id="total_weight_price_DisplayField">0.00</span>
                        </div>
                        <div class="col-12">
                            <span>Price from oversize : </span><span>&#163;</span><span id="addon_package_oversize_price_DisplayField">0.00</span>
                        </div>
                        <div class="col-12">
                            <span>Price from food : </span><span>&#163;</span><span id="addon_package_food_price_DisplayField">0.00</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12" id='packages_price_base_DisplayField'>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <span>Outside Zone Price : </span><span>&#163;</span><span id="total_outsideZone_price_DisplayField">0.00</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <span>Total timing : </span><span>&#163;</span><span id="total_timing_price_DisplayField">0.00</span>
                        </div>
                        <div class="col-12">
                            <span>Sunday : </span><span>&#163;</span><span id="addon_time_sunday_price_DisplayField">0.00</span>
                        </div>
                        <div class="col-12">
                            <span>Bank holiday : </span><span>&#163;</span><span id="addon_time_bankholiday_price_DisplayField">0.00</span>
                        </div>
                        <div class="col-12">
                            <span>Same day order : </span><span>&#163;</span><span id="addon_time_samedayorder_price_DisplayField">0.00</span>
                        </div>
                        <div class="col-12">
                            <span>Same day return : </span><span>&#163;</span><span id="addon_time_samedayreturn_price_DisplayField">0.00</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-12">
                                    <span>Pickup window : </span><span id="pickup_timing_value_DisplayField"></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <span>Price from pickup : </span><span>&#163;</span><span id="pickup_timing_price_DisplayField">0.00</span>
                                </div>
                            </div>    
                        </div>
                        <div class="col-12">
                            <div class="row">
                                <div class="col-12">
                                    <span>Dropoff window : </span><span id="dropoff_timing_value_DisplayField"></span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="row">
                                    <div class="col-12">
                                        <span>Price from dropOff : </span><span>&#163;</span><span id="dropoff_timing_price_DisplayField">0.00</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="row">
                                    <div class="col-12">
                                        <span>"Magic number" : </span><span>&#163;</span><span contenteditable="true" id="price_magicNumber_DisplayField" style="min-width: 5ch; display: inline-block;">0.00</span>
                                        <span id="magic_number_actions" style="display: none;">
                                            <button type="button" id="confirmMagicNumber" class="btn btn-success btn-sm">
                                                <i class="bi bi-check"></i>
                                            </button>
                                            <button type="button" id="cancelMagicNumber" class="btn btn-danger btn-sm">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>            
            </div>
        </div>
    </div>
</div>