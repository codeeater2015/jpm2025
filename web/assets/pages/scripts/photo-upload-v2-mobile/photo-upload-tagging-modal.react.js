var Modal = ReactBootstrap.Modal;
var FormGroup = ReactBootstrap.FormGroup
var HelpBlock = ReactBootstrap.HelpBlock;
var ControlLabel = ReactBootstrap.ControlLabel;
var FormControl = ReactBootstrap.FormControl;

var PhotoUploadTaggingModal = React.createClass({

    getInitialState: function () {
        return {
            form: {
                data: {
                    filename: "",
                    voterGroup: "",
                    proVoterId: null,
                    proIdCode: null,
                    generatedIdNo: null,
                    remarks: ""
                },
                errors: []
            },
            voter: {
                status: 'I'
            },

            showNewVoterCreateModal: false
        };
    },

    componentDidMount: function () {
        this.loadData(this.props.itemId);
    },


    loadData: function (id) {
        var self = this;

        self.requestVoter = $.ajax({
            url: Routing.generate("ajax_get_field_upload_item_detail", { id: id }),
            type: "GET"
        }).done(function (res) {
            var form = self.state.form;
            form.data = res;

            self.setState({ form: form }, self.initSelect2);
        });
    },

    initSelect2: function () {
        var self = this;

        $("#voter-tagging-form #municipality_select2").select2({
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
                            return { id: item.name, text: item.name };
                        })
                    };
                },
            }
        });

        $("#voter-tagging-form #barangay_select2").select2({
            casesentitive: false,
            placeholder: "Select Barangay",
            allowClear: true,
            delay: 1500,
            width: '100%',
            containerCssClass: ':all:',
            ajax: {
                url: Routing.generate('ajax_select2_barangay_alt'),
                data: function (params) {
                    return {
                        searchText: params.term,
                        municipalityName: $("#voter-tagging-form #municipality_select2").val(),
                        provinceCode: 53
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

        $("#form-voter-select2").select2({
            casesentitive: false,
            placeholder: "Enter Name...",
            allowClear: true,
            delay: 1000,
            width: '100%',
            containerCssClass: ':all:',
            dropdownCssClass: "custom-option",
            ajax: {
                url: Routing.generate('ajax_select2_project_voters_alt'),
                data: function (params) {
                    return {
                        searchText: params.term,
                        proId: 3,
                        electId: 423,
                        provinceCode: 53,
                        municipalityName: $("#municipality_select2").val(),
                        brgyNo: $("#barangay_select2").val()
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: data.map(function (item) {
                            var photo = parseInt(item.has_new_photo) == 1 ? "Y" : "N";
                            var id = parseInt(item.has_new_id) == 1 ? 'Y' : "N";
                            console.log(item);

                            var text = item.voter_name + ' - PHOTO(' + photo + ') ID(' + id + ") " + item.precinct_no + ' ( ' + item.municipality_name + ', ' + item.barangay_name + ' )';
                            return { id: item.pro_voter_id, text: text };
                        })
                    };
                },
            }
        });

        $("#reason_select2").select2({
            casesentitive: false,
            placeholder: "Select Reason",
            allowClear: true,
            delay: 1500,
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
                url: Routing.generate('ajax_select2_upload_reason'),
                data: function (params) {
                    return {
                        searchText: params.term
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: data.map(function (item) {
                            return { id: item.remarks, text: item.remarks };
                        })
                    };
                },
            }
        });

        $("#form-voter-select2").on("change", function () {
            console.log("voter id");
            console.log($(this).val());

            if (!self.isEmpty($(this).val())) {
                self.loadVoter(self.props.proId, $(this).val());
            }
        });

        $("#municipality_select2").on("change", function () {
            self.setFieldValue("municipalityName", $(this).val());
        });

        $("#barangay_select2").on("change", function () {
            self.setFieldValue("barangayNo", $(this).val());
        });

        $("#reason_select2").on("change", function () {
            self.setFieldValue("remarks", $(this).val());
        });

        $("#municipality_select2").empty()
            .append($("<option/>")
                .val(this.props.municipalityName)
                .text(this.props.municipalityName))
            .trigger("change");


        var data = self.state.form.data;

        $("#form-voter-select2").empty()
            .append($("<option/>")
                .val(data.proVoterId)
                .text(data.displayName))
            .trigger("change");

        $("#reason_select2").empty()
            .append($("<option/>")
                .val(data.remarks)
                .text(data.remarks))
            .trigger("change");
    },

    loadVoter: function (proId, proVoterId) {
        var self = this;
        self.requestVoter = $.ajax({
            url: Routing.generate("ajax_get_project_voter", { proId: 3, proVoterId: proVoterId }),
            type: "GET"
        }).done(function (res) {

            var form = self.state.form;
            form.data.proVoterId = res.proVoterId;
            form.data.proIdCode = res.proIdCode;
            form.data.voterName = res.voterName;
            form.data.generatedIdNo = res.generatedIdNo;
            form.data.cellphone = res.cellphone;

            self.setState({ form: form });
        });

        var form = self.state.form;

        form.data.proVoterId = null;
        form.data.voterId = null;
        form.data.cellphone = '';
        form.data.gender = '';
        form.data.remarks = '';
        form.data.position = '';

        self.setState({ form: form })
    },

    setFormProp: function (e) {
        var form = this.state.form;
        form.data[e.target.name] = e.target.value;
        this.setState({ form: form });
    },

    setFieldValue: function (field, value) {
        var form = this.state.form;
        form.data[field] = value;
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
        return this.getError(field) != null ? 'error' : '';
    },

    openNewVoterCreateModal: function () {
        this.setState({ showNewVoterCreateModal: true });
    },

    closeNewVoterCreateModal: function () {
        this.setState({ showNewVoterCreateModal: false })
    },

    isEmpty: function (value) {
        return value == null || value == '';
    },

    submit: function (e) {
        e.preventDefault();

        var self = this;
        var data = self.state.form.data;


        self.requestPost = $.ajax({
            url: Routing.generate('ajax_patch_photo_upload_item_v2', {
                id: this.props.itemId
            }),
            type: "PATCH",
            data: (data)
        }).done(function (res) {
            self.props.onHide();
            if (!self.isEmpty(res.generatedIdNo)) {
                self.props.onSuccess(res);
            }
        }).fail(function (err) {
            self.setErrors(err.responseJSON);
        });
    },

    closeNewVoterCreateModal : function(){
        this.setState({ showNewVoterCreateModal : false });
    },

    setFormCheckProp: function (e) {
        var form = this.state.form;
        form.data[e.target.name] = e.target.checked ? 1 : 0;
        this.setState({ form: form })
    },

    render: function () {
        var data = this.state.form.data;

        // if (!this.isEmpty(this.props.proVoterId)) {
        //     var generatedIdNo = this.state.form.data.generatedIdNo;
        //     var photoUrl = window.imgUrl + 3 + '_' + generatedIdNo + "?" + new Date().getTime();
        // }

        let imgUrl = Routing.generate("ajax_get_field_upload_photo_v2", { id: this.props.itemId });

        return (
            <Modal keyboard={false} enforceFocus={false} dialogClassName="modal-custom-full" backdrop="static" show={this.props.show} onHide={this.props.onHide}>
                <Modal.Header className="modal-header bg-blue-dark font-white" closeButton>
                    <Modal.Title>Tag Photo</Modal.Title>
                </Modal.Header>
                <Modal.Body bsClass="modal-body overflow-auto">

                    {
                        this.state.showNewVoterCreateModal &&
                        <VoterTemporaryCreateModal
                            proId="3"
                            electId="423"
                            provinceCode="53"
                            show={this.state.showNewVoterCreateModal}
                            onHide={this.closeNewVoterCreateModal}
                        />
                    }
                    <form id="voter-tagging-form">
                        <div className="row">

                            <div className="col-md-5">
                                <a className="thumbnail">
                                    <img src={imgUrl}></img>
                                </a>
                            </div>

                            <div className="col-md-12">
                                <FormGroup controlId="formProVoterId" validationState={this.getValidationState('proVoterId')}>
                                    <ControlLabel > Voter Name : </ControlLabel>
                                    <select id="form-voter-select2" className="form-control input-sm">
                                    </select>
                                    <HelpBlock>{this.getError('proVoterId')}</HelpBlock>
                                </FormGroup>
                            </div>

                            <div className="col-md-12">
                                <FormGroup controlId="formFileDisplayName" validationState={this.getValidationState('fileDisplayName')}>
                                    <ControlLabel > Photo Name : </ControlLabel>
                                    <FormControl type="text" bsClass="form-control input-sm" name="fileDisplayName" value={this.state.form.data.fileDisplayName} onChange={this.setFormProp} />
                                    <HelpBlock>{this.getError('fileDisplayName')}</HelpBlock>
                                </FormGroup>
                            </div>

                            <div className="col-md-12">
                                <label className="mt-checkbox">
                                    <input type="checkbox" name="isNotFound" checked={data.isNotFound == 1} onChange={this.setFormCheckProp} />
                                    Not Found
                                    <span></span>
                                </label>
                            </div>
                            <div className="col-md-12">
                                <FormGroup controlId="formRemarks">
                                    <ControlLabel > Reason : </ControlLabel>
                                    <select id="reason_select2" className="form-control form-filter input-sm" name="reason">
                                    </select>
                                </FormGroup>
                            </div>
                        </div>

                        <div className="row">
                            <div className="col-md-6 col-sm-6 col-xs-6 text-left">
                                <button onClick={this.openNewVoterCreateModal} className="btn btn-primary" type="button"> New Voter / Transfer </button>
                            </div>
                            <div className="col-md-6 col-sm-6 col-xs-6 text-right">
                                <button type="button" className="btn blue-madison" style={{ paddingRight:"20px" , paddingLeft: "20px" }} onClick={this.submit}>Submit Form</button>
                            </div>
                        </div>

                    </form>

                </Modal.Body>
            </Modal >
        );
    }

});

window.PhotoUploadTaggingModal = PhotoUploadTaggingModal;