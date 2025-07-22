var Modal = ReactBootstrap.Modal;
var FormGroup = ReactBootstrap.FormGroup
var HelpBlock = ReactBootstrap.HelpBlock;
var ControlLabel = ReactBootstrap.ControlLabel;
var FormControl = ReactBootstrap.FormControl;

var GroupAssistanceCreateModal = React.createClass({

    getInitialState: function () {
        return {
            showNewProfileModal: false,
            form: {
                data: {
                    finalBill: 0,
                    amount: 0,
                    hospital: "",
                    dependentDiagnosis: "",
                    transDate: "",
                    transType: "",
                    remarks: "",
                    clientProfileId: null,
                    dependentProfileId: null
                },
                errors: []
            }
        };
    },

    render: function () {
        var self = this;
        var data = this.state.form.data;

        return (
            <Modal style={{ marginTop: "10px" }} bsSize="lg" keyboard={false} enforceFocus={false} backdrop="static" show={this.props.show} onHide={this.props.onHide}>
                <Modal.Header className="modal-header bg-blue-dark font-white" closeButton>
                    <Modal.Title>New Assistance Form</Modal.Title>
                </Modal.Header>
                <Modal.Body bsClass="modal-body overflow-auto">

                    {
                        this.state.showNewProfileModal &&
                        <AssistanceNewProfileModal
                            proId={3}
                            electId={4}
                            provinceCode={53}
                            show={this.state.showNewProfileModal}
                            onHide={this.closeNewProfileModal}
                        />
                    }

                    <form id="fa-group-form" >
                        <div className="row">
                            <div className="col-md-12">

                                <div className="row">
                                    <div className="col-md-3">
                                        <FormGroup controlId="formMunicipalityName" validationState={this.getValidationState('municipalityName')}>
                                            <label className="control-label">City/Municipality</label>
                                            <select id="municipality_select2" className="form-control form-filter input-sm" name="municipalityName">
                                            </select>
                                            <HelpBlock>{this.getError('municipalityName')}</HelpBlock>
                                        </FormGroup>
                                    </div>

                                    <div className="col-md-3">
                                        <FormGroup controlId="formAssistType" validationState={this.getValidationState('assistType')}>
                                            <label className="control-label">Assistance Type</label>
                                            <select id="assist_type_select2" className="form-control form-filter input-sm" name="assistType">
                                            </select>
                                            <HelpBlock>{this.getError('assistType')}</HelpBlock>
                                        </FormGroup>
                                    </div>
                                </div>

                                <div className="row">
                                    <div className="col-md-3">
                                        <FormGroup controlId="formBatchDate" validationState={this.getValidationState('batchDate')}>
                                            <ControlLabel> Date : </ControlLabel>
                                            <input type="date" value={this.state.form.data.batchDate} className="input-sm form-control" onChange={this.setFormProp} name="batchDate" />
                                            <HelpBlock>{this.getError('batchDate')}</HelpBlock>
                                        </FormGroup>
                                    </div>
                                    <div className="col-md-5">
                                        <FormGroup controlId="formBatchLabel" validationState={this.getValidationState('batchLabel')}>
                                            <ControlLabel> Batch Name : </ControlLabel>
                                            <input type="text" value={this.state.form.data.batchLabel} className="input-sm form-control" onChange={this.setFormProp} name="batchLabel" />
                                            <HelpBlock>{this.getError('batchLabel')}</HelpBlock>
                                        </FormGroup>
                                    </div>
                                </div>


                                <div className="row">
                                    <div className="col-md-12">
                                        <FormGroup controlId="formRemarks" validationState={this.getValidationState('remarks')}>
                                            <ControlLabel> Remarks / Description : </ControlLabel>
                                            <FormControl componentClass="textarea" rows="6" bsClass="form-control input-sm" name="remarks" value={this.state.form.data.remarks} onChange={this.setFormProp} />
                                            <HelpBlock>{this.getError('remarks')}</HelpBlock>
                                        </FormGroup>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div className="clearfix" />

                        <div className="text-right" >
                            <button type="button" className="btn btn-default" style={{ marginRight: "5px" }} onClick={this.props.onHide}>Close</button>
                            <button type="button" className="btn btn-primary" onClick={this.submit}>Submit</button>
                        </div>
                    </form>
                </Modal.Body>
            </Modal>
        );
    },

    componentDidMount: function () {
        this.initSelect2();
    },

    loadClient: function (id) {
        var self = this;

        self.requestVoter = $.ajax({
            url: Routing.generate("ajax_get_assistance_profile", { id: id }),
            type: "GET"
        }).done(function (res) {
            var form = self.state.form;
            form.data.contactNo = res.contactNo;
            form.data.clientAddress = res.purok + " " + res.barangayName + ", " + res.municipalityName;

            self.setState({ form: form });
        });
    },

    loadDependent: function (id) {
        var self = this;

        self.requestVoter = $.ajax({
            url: Routing.generate("ajax_get_assistance_profile", { id: id }),
            type: "GET"
        }).done(function (res) {
            var form = self.state.form;
            form.data.dependentContactNo = res.contactNo;
            form.data.dependentAddress = res.purok + " " + res.barangayName + ", " + res.municipalityName;

            self.setState({ form: form });
        });
    },

    initSelect2: function () {
        var self = this;

        $("#fa-group-form #municipality_select2").select2({
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
                        provinceCode: self.state.provinceCode
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: data.map(function (item) {
                            return { id: item.name, text: item.name };
                        })
                    };
                },
            }
        });

        $("#fa-group-form #assist_type_select2").select2({
            casesentitive: false,
            placeholder: "Select...",
            allowClear: true,
            width: '100%',
            containerCssClass: ':all:',
            tags: true,
            createTag: function (params) {
                return {
                    id: params.term,
                    text: params.term,
                    newOption: true
                }
            },
            ajax: {
                url: Routing.generate('ajax_group_assist_type_select2'),
                data: function (params) {
                    return {
                        searchText: params.term
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: data.map(function (item) {
                            return { id: item.assist_type, text: item.assist_type };
                        })
                    };
                },
            }
        });

        $("#fa-group-form #municipality_select2").on("change", function () {
            self.setFormPropValue('municipalityName', $(this).val());
        });

        $("#fa-group-form #assist_type_select2").on("change", function () {
            self.setFormPropValue('assistType', $(this).val());
        });
    },

    setFormPropValue: function (field, value) {
        var form = this.state.form;
        form.data[field] = value;
        this.setState({ form: form });
    },

    setFormProp: function (e) {
        var form = this.state.form;
        form.data[e.target.name] = e.target.value;
        this.setState({ form: form });
    },

    setFormCheckProp: function (e) {
        var form = this.state.form;
        form.data[e.target.name] = e.target.checked ? 1 : 0;
        this.setState({ form: form })
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
        return this.getError(field) != null ? 'error' : '';
    },

    isEmpty: function (value) {
        return value == null || value == '';
    },

    reset: function () {
        var form = this.state.form;
        form.errors = [];

        this.setState({ form: form });
    },

    openNewProfileModal: function () {
        var self = this;

        self.setState({ showNewProfileModal: true });
    },

    closeNewProfileModal: function () {
        var self = this;

        self.setState({ showNewProfileModal: false });
    },

    submit: function (e) {
        e.preventDefault();

        var self = this;
        var data = self.state.form.data;
        data.proId = self.props.proId;

        self.requestPost = $.ajax({
            url: Routing.generate("ajax_post_jpm_group_assistance"),
            data: data,
            type: 'POST'
        }).done(function (res) {
            self.reset();
            self.props.onSuccess();
            self.props.onHide();
        }).fail(function (err) {
            self.setErrors(err.responseJSON);
        });
    }
});


window.GroupAssistanceCreateModal = GroupAssistanceCreateModal;