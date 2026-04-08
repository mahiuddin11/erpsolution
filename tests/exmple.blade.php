<div class="col-md-2 mb-3">
                            <label for="ledger_id">Ledger * : 
                                <button type="button" class="btn btn-sm btn-primary ml-2" data-toggle="modal"
                                data-target="#addCustomerModel">
                                +
                            </button>
                            </label>
                            <select class="form-control select2" name="ledger_id" id="ledger_id">
                                <option selected disabled value="">--Select Ledger--</option>
                                <x-account :setAccounts="$ledgers" />
                            </select>
                            @error('ledger_id')
                            <span class=" error text-red text-bold">{{ $message }}</span>
                            @enderror
                        </div>