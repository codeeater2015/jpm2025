var Modal = ReactBootstrap.Modal;
var FormGroup = ReactBootstrap.FormGroup
var HelpBlock = ReactBootstrap.HelpBlock;
var ControlLabel = ReactBootstrap.ControlLabel;
var FormControl = ReactBootstrap.FormControl;

var DataUpdaterModal = React.createClass({

    getInitialState: function () {
        return {
            unselected: [],
            options: [],
            form: {
                data: {
                    projectVoters: [],

                },
                errors: []
            },

            isLoading: false,
            progressValue: 0,
            availableData: []
        };
    },

    componentDidMount: function () {
        this.initComponents();
        this.initSelect2();
        //this.loadProjectVoters();
    },


    loadAvailableData: function () {
        var self = this;

        self.requestProjectVoters = $.ajax({
            url: Routing.generate("ajax_get_available_data", {
                proId: this.props.proId,
                electId: this.props.electId,
                startDate: $('#downloader_modal #start_date_input').val(),
                endDate: $('#downloader_modal #end_date_input').val(),
                municipalityNo: $('#downloader_modal #municipality_select2').val(),
                brgyNo: $('#downloader_modal #barangay_select2').val()
            }),
            type: "GET"
        }).done(function (res) {
            console.log('available data has been received', res);
            self.setState({ availableData: res });
        });
    },

    loadProjectVoters: function () {
        var self = this;

        self.requestProjectVoters = $.ajax({
            url: Routing.generate("ajax_get_did_change_voter", {
                proId: this.props.proId,
                electId: this.props.electId
            }),
            type: "GET"
        }).done(function (res) {
            self.setState({ options: res, unselected: res });
            setTimeout(self.refreshSelectBox, 2000);
        });
    },


    initSelect2: function () {
        var self = this;

        $("#downloader_modal #municipality_select2").select2({
            casesentitive: false,
            placeholder: "Select City/Municipality",
            allowClear: true,
            delay: 1500,
            width: '100%',
            containerCssClass: ':all:',
            ajax: {
                url: Routing.generate('ajax_select2_municipality'),
                data: function (params) {
                    return {
                        searchText: params.term,
                        provinceCode: 53
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: data.map(function (item) {
                            return { id: item.municipality_no, text: item.name };
                        })
                    };
                },
            }
        });

        
        $("#downloader_modal #barangay_select2").select2({
            casesentitive: false,
            placeholder: "Enter name...",
            allowClear: true,
            delay: 1500,
            width: '100%',
            containerCssClass: ':all:',
            ajax: {
                url: Routing.generate('ajax_select2_barangay'),
                data: function (params) {
                    return {
                        searchText: params.term,
                        provinceCode: 53,
                        municipalityNo: $("#municipality_select2").val()
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: data.map(function (item) {
                            return { id: item.brgy_no, text: item.name };
                        })
                    };
                },
            }
        });

    },

    componentWillUnmount: function () {
        this.isEmpty(this.requestProjectVoters) || this.requestProjectVoters.abort();
    },


    initComponents: function () {
        this.initMultiSelect();
    },

    initMultiSelect: function () {
        var self = this;

        var selectBox = this.refs.selectBox;

        $(selectBox).multiSelect({
            selectableOptgroup: true,
            selectableHeader: "<input placeholder='Enter Name' type='text' className='form-control' autocomplete='off' style='text-transform:uppercase;margin-bottom:5px;'>",
            selectionHeader: "<input placeholder='Enter Name' type='text' className='form-control' autocomplete='off' style='text-transform:uppercase;margin-bottom:5px;'>",
            afterInit: function (ms) {
                var that = this,
                    $selectableSearch = that.$selectableUl.prev(),
                    $selectionSearch = that.$selectionUl.prev(),
                    selectableSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selectable:not(.ms-selected)',
                    selectionSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selection.ms-selected';

                that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
                    .on('keydown', function (e) {
                        if (e.which === 40) {
                            that.$selectableUl.focus();
                            return false;
                        }
                    });

                that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
                    .on('keydown', function (e) {
                        if (e.which == 40) {
                            that.$selectionUl.focus();
                            return false;
                        }
                    });
            },

            afterSelect: function (values) {
                this.qs1.cache();
                this.qs2.cache();
                self.setProjectVoters($(self.refs.selectBox).val());
            },

            afterDeselect: function (values) {
                this.qs1.cache();
                this.qs2.cache();
                self.setProjectVoters($(self.refs.selectBox).val());
            },
            cssClass: "fluid-size"
        });


    },

    refreshSelectBox: function () {
        $(this.refs.selectBox).multiSelect('refresh');
    },

    deselectAll: function () {
        $(this.refs.selectBox).multiSelect('deselect_all');
    },

    selectAll: function () {
        $(this.refs.selectBox).multiSelect('select_all');
    },

    setProjectVoters: function (selected) {
        var form = this.state.form;
        var unselected = [];

        if (selected != null) {
            form.data.projectVoters = selected;
            unselected = this.state.options.filter(function (item) {
                return selected.indexOf(item.pro_voter_id) == -1;
            });
        } else {
            form.data.projectVoters = [];
            unselected = this.state.options;
        }

        this.setState({ form: form, unselected: unselected });
    },

    setFormProp: function (e) {
        var form = this.state.form;
        form.data[e.target.name] = e.target.value;
        this.setState({ form: form });
    },

    setErrors: function (errors) {
        var form = this.state.form;
        form.errors = errors;
        this.setState({ form: form });
    },

    getError: function (field) {
        var errors = this.state.form.errors;
        for (var errorField in errors) {
            if (errorField == field)
                return errors[field];
        }

        return null;
    },

    getValidationState: function (field) {
        if (this.getError(field) != null)
            return "error";

        return null;
    },

    submit: function (e) {
        e.preventDefault();

        var self = this;
        var data = {
            proId: this.props.proId,
            electId: this.props.electId,
            startDate: $('#downloader_modal #start_date_input').val(),
            endDate: $('#downloader_modal #end_date_input').val(),
            municipalityNo: $('#downloader_modal #municipality_select2').val(),
            brgyNo: $('#downloader_modal #barangay_select2').val()
        };

        var lastResponseLength = false;

        self.requestPostUpdate = $.ajax({
            url: Routing.generate("ajax_post_updated_records"),
            type: "POST",
            data: data,
            xhrFields: {
                onprogress: function (e) {
                    var progressResponse;
                    var response = e.currentTarget.response;

                    if (lastResponseLength === false) {
                        progressResponse = response;
                        lastResponseLength = response.length;
                    }
                    else {
                        progressResponse = response.substring(lastResponseLength);
                        lastResponseLength = response.length;
                    }

                    self.setState({ progressValue: parseInt(progressResponse) });
                }
            }
        }).done(function (res) {
            console.log("data has been received");
            // self.props.notify("Data has been imported", "ruby");
            // self.props.onHide();
            // self.props.onSuccess();
        }).fail(function (err) {
            console.log('something went wrong');
            // self.props.notify("Opps!", "ruby");
            // self.setErrors(err.responseJSON)
        }).always(function () {
            self.setState({ isLoading: false, progressValue: 0 });
        });

        self.setState({ isLoading: true, progressValue: 0 });

        // var intervalHandle = setInterval(function(){
        // 	if(self.state.progressValue < 100){
        // 		var value = self.state.progressValue;
        // 		console.log('adding');
        // 		value += 1;
        // 		console.log(value);
        // 		self.setState({ progressValue : value });
        // 	}else{

        // 		console.log('reset');

        // 		self.setState({ progressValue : 0 , isLoading : false });
        // 		clearInterval(intervalHandle);
        // 	}

        // },500);

        // self.setState({ isLoading : true });

        // e.preventDefault();
        // var self = this;
        // var data = self.state.form.data;

        // self.requestTransmit = $.ajax({
        //     url : Routing.generate('ajax_post_updated_records'),
        //     type : 'POST',
        //     data : (data)
        // }).done(function(res){
        //     self.props.onHide();
        // }).fail(function(res){
        //     self.setErrors(res.responseJSON);
        // });
    },

    popupCenter: function (url, title, w, h) {
        // Fixes dual-screen position                         Most browsers      Firefox  
        var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
        var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;
        var width = 0;
        var height = 0;

        width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
        height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

        var left = ((width / 2) - (w / 2)) + dualScreenLeft;
        var top = ((height / 2) - (h / 2)) + dualScreenTop;
        var newWindow = window.open(url, title, 'scrollbars=yes, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);

        // Puts focus on the newWindow  
        if (window.focus) {
            newWindow.focus();
        }
    },

    isEmpty: function (value) {
        return value == null || value == '';
    },

    render: function () {
        return (
            <Modal enforceFocus={false} backdrop="static" show={this.props.show} onHide={this.props.onHide}>
                <Modal.Header closeButton>
                    <Modal.Title>Downloader Modal</Modal.Title>
                </Modal.Header>

                <Modal.Body bsClass="modal-body overflow-auto">
                    <div id="downloader_modal">
                        <form>
                            <div className="row">
                                <div className="col-md-6">
                                    <select id="municipality_select2" className="form-control form-filter input-sm" >
                                    </select>
                                </div>
                                <div className="col-md-6">
                                    <select id="barangay_select2" className="form-control form-filter input-sm" >
                                    </select>
                                </div>
                            </div>
                            <div className="row">
                                <div className="col-md-6">
                                    <div className="form-group">
                                        <label className="control-label">Start Date</label>
                                        <input id="start_date_input" className="form-control form-control-inline" type="date" ></input>
                                    </div>
                                </div>
                                <div className="col-md-6">
                                    <div className="form-group">
                                        <label className="control-label">End Date</label>
                                        <input id="end_date_input" className="form-control form-control-inline " type="date"></input>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div>

                        <div className="portlet box default">
                            <div className="portlet-title">
                                <div className="caption">
                                    <i className="fa fa-picture"></i>Available data for download</div>
                                <div className="tools">
                                    <a href="javascript:;" className="collapse"> </a>
                                </div>
                            </div>
                            <div className="portlet-body">
                                <div className="table-scrollable">
                                    <table className="table table-bordered table-condensed table-hover">
                                        <thead>
                                            <tr>
                                                <th className="text-center">Municipality</th>
                                                <th className="text-center">BarangayName</th>
                                                <th className="text-center">Total Items</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {this.state.availableData.map((item) => {
                                                return (<tr>
                                                    <td className="text-center">{item.municipality_name}</td>
                                                    <td className="text-center">{item.barangay_name}</td>
                                                    <td className="text-center">{item.total_items}</td>
                                                </tr>)
                                            })}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                    <form onSubmit={this.submit}>

                        {this.state.isLoading && (
                            <div className="progress">
                                <div className="progress-bar" role="progressbar" aria-valuenow={this.state.progressValue} aria-valuemin="0" aria-valuemax="100" style={{ width: this.state.progressValue + "%" }}>
                                    <span> {this.state.progressValue} % Complete</span>
                                </div>
                            </div>
                        )}

                        <div className="row">
                            <div className="col-md-6 pull-left">
                                <button type="button" style={{ marginRight: "10px" }} onClick={this.loadAvailableData} className="btn green-seagreen">Check Available Data</button>
                            </div>
                            <div className=" col-md-6 text-right ">
                                <button type="button" className="btn btn-default" style={{ marginRight: "10px" }} onClick={this.props.onHide}>Cancel</button>
                                <button type="submit" className="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </form>
                </Modal.Body>
            </Modal>
        );
    }
});


window.DataUpdaterModal = DataUpdaterModal;