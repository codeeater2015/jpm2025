var Modal = ReactBootstrap.Modal;
var FormGroup = ReactBootstrap.FormGroup
var HelpBlock = ReactBootstrap.HelpBlock;
var ControlLabel = ReactBootstrap.ControlLabel;
var FormControl = ReactBootstrap.FormControl;

var PhotoUploadItemEditModal = React.createClass({

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
            url: Routing.generate("ajax_get_field_upload_item_detail_jtr", { id: id }),
            type: "GET"
        }).done(function (res) {
            var form = self.state.form;
            form.data = res;

            self.setState({ form: form }, self.initSelect2);
        });
    },

    initSelect2: function () {
        var self = this;

        $("#municipality_select2").select2({
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

        $("#barangay_select2").select2({
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
                        municipalityName: $("#municipality_select2").val(),
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
                            var photo = parseInt(item.has_new_photo) == 1 ? "Yes : Taken new photo" : "No";
                            var id = parseInt(item.has_new_id) == 1 ? 'Yes : Printed New Id' : "No";
                            console.log(item);

                            var text = item.voter_name + ' - PHOTO (' + photo + ') ID (' + id + ") " + item.precinct_no + ' ( ' + item.municipality_name + ', ' + item.barangay_name + ' )';
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

        // $("#municipality_select2").empty()
        //     .append($("<option/>")
        //         .val(this.props.municipalityName)
        //         .text(this.props.municipalityName))
        //     .trigger("change");


        var data = self.state.form.data;

        $("#form-voter-select2").empty()
            .append($("<option/>")
                .val(data.proVoterId)
                .text(data.displayName))
            .trigger("change");

        console.log("remarks");
        console.log(data.remarks);

        $("#reason_select2").empty()
            .append($("<option/>")
                .val(data.remarks)
                .text(data.remarks))
            .trigger("change");

        //$('.zoom').magnify();
        //var $easyzoom = $('.easyzoom').easyZoom();
        console.log("magnify image");
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
            form.data.hasNewPhoto = res.hasNewPhoto;
            form.data.hasNewId = res.hasNewId;

            self.setState({ form: form });
        });

        var form = self.state.form;

        form.data.proVoterId = null;
        form.data.voterId = null;
        form.data.cellphone = '';
        form.data.gender = '';
        form.data.remarks = '';
        form.data.position = '';
        form.data.hasNewPhoto = 0;
        form.data.hasNewId = 0;

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
            url: Routing.generate('ajax_patch_photo_upload_item_jtr', {
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

    
    tagJtr: function (e) {
        e.preventDefault();

        var self = this;
        var data = self.state.form.data;

        self.requestPost = $.ajax({
            url: Routing.generate('ajax_patch_photo_upload_item_no_photo_jtr', {
                id: this.props.itemId
            }),
            type: "PATCH",
            data: (data)
        }).done(function (res) {
            self.props.onHide();
        }).fail(function (err) {
            self.setErrors(err.responseJSON);
        });
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

        let imgUrl = Routing.generate("ajax_get_field_upload_photo_jtr", { id: this.props.itemId });

        return (
            <Modal keyboard={false} enforceFocus={false} dialogClassName="modal-custom-85" backdrop="static" show={this.props.show} onHide={this.props.onHide}>
                <Modal.Header className="modal-header bg-blue-dark font-white" closeButton>
                    <Modal.Title>Edit File</Modal.Title>
                </Modal.Header>
                <Modal.Body bsClass="modal-body overflow-auto">

                    {
                        this.state.showNewVoterCreateModal &&
                        <VoterTemporaryCreateModal
                            proId="3"
                            electId="3"
                            provinceCode="3"
                            show={this.state.showNewVoterCreateModal}
                            notify={this.props.notify}
                            onHide={this.closeNewVoterCreateModal}
                        />
                    }
                    <form id="voter-updated-form">
                        <div className="row">
                            <div className="col-md-5">
                                <a className="thumbnail">
                                    <img className="zoom" data-magnify-src={imgUrl} src={imgUrl}></img>
                                </a>
                            </div>

                            <div className="col-md-7">
                                <div className="row">
                                    <div className="col-md-4">
                                        <FormGroup controlId="formMunicipalityNo">
                                            <ControlLabel > Municipality : </ControlLabel>
                                            <select id="municipality_select2" className="form-control form-filter input-sm" name="municipalityNo">
                                            </select>
                                        </FormGroup>
                                    </div>

                                    <div className="col-md-4">
                                        <FormGroup controlId="formBrgyNo">
                                            <ControlLabel > Barangay : </ControlLabel>
                                            <select id="barangay_select2" className="form-control form-filter input-sm" name="brgyNo">
                                            </select>
                                        </FormGroup>
                                    </div>
                                </div>

                                <div className="row">
                                    <div className="col-md-8">
                                        <FormGroup controlId="formFileDisplayName" validationState={this.getValidationState('fileDisplayName')}>
                                            <ControlLabel > Photo Name : </ControlLabel>
                                            <FormControl type="text" bsClass="form-control input-sm" name="fileDisplayName" value={this.state.form.data.fileDisplayName} onChange={this.setFormProp} />
                                            <HelpBlock>{this.getError('fileDisplayName')}</HelpBlock>
                                        </FormGroup>
                                    </div>
                                    <div className="col-md-3">
                                        <button style={{ marginTop: "27px" }} onClick={this.openNewVoterCreateModal} className="btn btn-primary btn-sm" type="button"> Non-voter </button>
                                    </div>
                                </div>

                                <div className="row">
                                    <div className="col-md-6">
                                        <FormGroup controlId="formCellphone" validationState={this.getValidationState('cellphone')}>
                                            <ControlLabel > Cellphone : </ControlLabel>
                                            <FormControl type="text" bsClass="form-control input-sm" name="cellphone" value={this.state.form.data.cellphone} onChange={this.setFormProp} />
                                            <HelpBlock>{this.getError('cellphone')}</HelpBlock>
                                        </FormGroup>
                                    </div>
                                </div>

                                <div className="row">
                                    <div className="col-md-8">
                                        <FormGroup controlId="formProVoterId" validationState={this.getValidationState('proVoterId')}>
                                            <ControlLabel > Voter Name : </ControlLabel>
                                            <select id="form-voter-select2" className="form-control input-sm">
                                            </select>
                                            <HelpBlock>{this.getError('proVoterId')}</HelpBlock>
                                        </FormGroup>
                                    </div>

                                </div>

                                <div className="row">
                                    <div className="col-md-4">
                                        <label className="mt-checkbox">
                                            <input type="checkbox" name="isNotFound" checked={data.isNotFound == 1} onChange={this.setFormCheckProp} />
                                            Not Found
                                            <span></span>
                                        </label>
                                    </div>
                                    <div className="col-md-4">
                                        <FormGroup controlId="formRemarks">
                                            <ControlLabel > Reason : </ControlLabel>
                                            <select id="reason_select2" className="form-control form-filter input-sm" name="reason">
                                            </select>
                                        </FormGroup>
                                    </div>
                                </div>


                                {
                                    //     <div className="row">
                                    //     <div className="col-md-8">
                                    //         <FormGroup controlId="formFilename" validationState={this.getValidationState('filename')}>
                                    //             <ControlLabel > Position : </ControlLabel>
                                    //             <FormControl type="text" bsClass="form-control input-sm" name="filename" value={this.state.form.data.filename} onChange={this.setFormProp} />
                                    //             <HelpBlock>{this.getError('filename')}</HelpBlock>
                                    //         </FormGroup>
                                    //     </div>
                                    // </div>
                                }
                            </div>
                        </div>

                        <div className="row">
                            <div className="col-md-12">
                                <div className="text-right" >
                                    { ((data.proVoterId != null && data.proVoterId != "") || data.isNotFound == 1) && (Number.parseInt(data.hasNewPhoto) == 0 || data.hasNewPhoto == "" ) && <button type="button" className="btn blue-madison" style={{ marginRight: "5px" }} onClick={this.submit}>Submit & Crop</button>}
                                    { (data.isNotFound == 1) && <button type="button" className="btn blue-madison" style={{ marginRight: "5px" }} onClick={this.submit}>Submit & Crop</button>}
                                    { ((data.proVoterId != null && data.proVoterId != "") || data.isNotFound == 1) && (Number.parseInt(data.hasNewPhoto) == 1  ) && <button type="button" className="btn blue-madison" style={{ marginRight: "5px" }} onClick={this.tagJtr}>Reprint ID</button>}
                                    <button type="button" className="btn btn-default" onClick={this.props.onHide}>Close</button>
                                </div>
                            </div>
                        </div>

                    </form>

                </Modal.Body>
            </Modal >
        );
    }

});

window.PhotoUploadItemEditModal = PhotoUploadItemEditModal;