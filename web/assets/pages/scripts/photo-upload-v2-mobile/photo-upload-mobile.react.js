var FormGroup = ReactBootstrap.FormGroup
var HelpBlock = ReactBootstrap.HelpBlock;
var ControlLabel = ReactBootstrap.ControlLabel;
var FormControl = ReactBootstrap.FormControl;

var PhotoUpload = React.createClass({

    notify: function (message, color) {
        $.notific8('zindex', 11500);
        $.notific8(message, {
            heading: 'System Message',
            color: color,
            life: 5000,
            verticalEdge: 'right',
            horizontalEdge: 'top',
        });
    },

    componentDidMount: function () {
        this.loadUser(window.userId);
        this.initSelect2();
        this.loadBreakdown();
        this.loadData();
    },

    getInitialState: function () {
        return {
            municipalityName: null,
            brgyNo: null,
            showDatatable: false,
            breakdownList: [],
            items: [],
            showTaggingModal: false,
            targetId: null,
            data: null,
            showCropModal: false,
            showCropTaggedModal : false
        };
    },




    loadBreakdown: function () {
        var self = this;
        let municipalityName = self.state.municipalityName;

        let url = "";

        if (municipalityName != "" && municipalityName != null) {
            url = Routing.generate("ajax_m_get_field_photos_remaining_per_barangay", {
                municipalityName: municipalityName
            });
        } else {
            url = Routing.generate("ajax_get_photo_upload_v2_remaining_photos_per_municipality");
        }

        self.requestHierarchyData = $.ajax({
            url: url,
            type: "GET"
        }).done(function (res) {
            console.log("breakdown list has been received");
            console.log(res);
            self.setState({ breakdownList: res });
        });
    },


    loadUser: function (userId) {
        var self = this;

        self.requestUser = $.ajax({
            url: Routing.generate("ajax_get_user", { id: userId }),
            type: "GET"
        }).done(function (res) {
            self.setState({ user: res }, self.initSelect2);
        });
    },

    initSelect2: function () {
        var self = this;

        $("#form-municipality-select2").select2({
            casesentitive: false,
            placeholder: "Enter municipality...",
            width: '100%',
            allowClear: true,
            tags: true,
            containerCssClass: ":all:",
            createTag: function (params) {
                return {
                    id: params.term,
                    text: params.term,
                    newOption: true
                }
            },
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

        $("#form-barangay-select2").select2({
            casesentitive: false,
            placeholder: "Enter name...",
            allowClear: true,
            delay: 1500,
            width: '100%',
            containerCssClass: ':all:',
            ajax: {
                url: Routing.generate('ajax_select2_barangay_alt'),
                data: function (params) {
                    return {
                        searchText: params.term,
                        provinceCode: 53,
                        municipalityName: $("#form-municipality-select2").val()
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

        $("#form-municipality-select2").on("change", function () {
            $("#form-barangay-select2").empty()
                .trigger("change");

            self.setState({ "municipalityName": $(this).val() });
        });

        $("#form-barangay-select2").on("change", function () {
            self.setState({ "brgyNo": $(this).val() });
        });
    },

    loadData: function () {
        var self = this;

        let url = Routing.generate("ajax_get_photo_uploads_v2_for_tagging", {
            municipalityName: self.state.municipalityName,
            brgyNo: self.state.brgyNo,
            pageSize: $('#pending-photo-portlet #select-page-size').val(),
            photoGroup: $('#pending-photo-portlet #select-photo-group').val()
        });

        self.requestHierarchyData = $.ajax({
            url: url,
            type: "GET"
        }).done(function (res) {
            console.log("pending photos for tagging has been received.");
            console.log(res.length);

            self.setState({ items: res });
        });
    },

    reloadDatatable: function () {
        console.log("reloading datatable");
        if (this.state.showDatatable)
            this.refs.fieldDatatable.reload();
    },

    closeTaggingModal: function () {
        this.setState({ showTaggingModal: false, targetId: null });
    },

    openTaggingModal: function (item) {
        this.setState({ showTaggingModal: true, targetId: item.id });
    },

    openCropTaggedModal: function (data) {
        console.log('open crop tagged modal', data);
        this.setState({ showCropTaggedModal: true, data: data })
    },

    openCropModal: function (data) {
        this.setState({ showCropModal: true, data: data })
    },

    closeCropModal: function () {
        this.setState({ showCropModal: false, data: null });
    },

    closeCropTaggedModal : function(){
        this.setState({ showCropTaggedModal : false, data : null});
    },

    render: function () {
        let self = this;

        return (
            <div>
                <div className="row">

                    {
                        this.state.showTaggingModal &&
                        (
                            <PhotoUploadTaggingModal
                                show={this.state.showTaggingModal}
                                onHide={this.closeTaggingModal}
                                itemId={this.state.targetId}
                                municipalityName={this.state.municipalityName}
                                barangayName={this.state.barangayName}
                                brgyNo={this.state.brgyNo}
                                onSuccess={this.openCropModal}
                            />
                        )
                    }

                    {
                        this.state.showCropModal &&
                        (
                            <VoterCropModal
                                proId="3"
                                proVoterId={this.state.data.proVoterId}
                                itemId={this.state.data.id}
                                generatedIdNo={this.state.data.generatedIdNo}
                                show={this.state.showCropModal}
                                onHide={this.closeCropModal}
                                onSuccess={this.loadData}
                            />
                        )
                    }

                    {
                        this.state.showCropTaggedModal &&
                        (
                            <VoterCropTaggedModal
                                proId={3}
                                proVoterId={this.state.data.pro_voter_id}
                                generatedIdNo={this.state.data.generated_id_no}
                                show={this.state.showCropTaggedModal}
                                onHide={this.closeCropTaggedModal}
                                onSuccess={this.loadData}
                            />
                        )
                    }

                    <div className="col-md-4">
                        <div className="portlet light portlet-fit bordered">
                            <div className="portlet-body">
                                <FormGroup controlId="formBarangayNo">
                                    <ControlLabel > Municipality : </ControlLabel>
                                    <select id="form-municipality-select2" className="form-control input-sm">
                                    </select>
                                </FormGroup>

                                <FormGroup controlId="formBarangayNo">
                                    <ControlLabel > Barangay : </ControlLabel>
                                    <select id="form-barangay-select2" className="form-control input-sm">
                                    </select>
                                </FormGroup>
                            </div>
                        </div>

                        <div className="clearfix"></div>

                        <div className="mt-element-list">
                            <div className="mt-list-head list-simple ext-1 font-white bg-green-sharp">
                                <div className="list-head-title-container">
                                    <h3 className="list-title ">Todo List</h3>
                                </div>
                            </div>
                            <div className="mt-list-container list-simple ext-1">
                                <ul>
                                    {
                                        self.state.breakdownList.map((item) => {
                                            let difference = Number.parseInt(item.total_photos) - Number.parseInt(item.total_unlinked_photo);

                                            if (Number.parseInt(item.total_unlinked_photo) == 0) {
                                                return (
                                                    <li className="mt-list-item done">
                                                        <div className="list-icon-container">
                                                            <i className="icon-check"></i>
                                                        </div>
                                                        <div className="list-datetime"> {item.total_unlinked_photo} / {item.total_photos} </div>
                                                        <div className="list-item-content">
                                                            <h3 className="uppercase">
                                                                <a href="javascript:;">{item.municipality_name} </a>
                                                            </h3>
                                                        </div>
                                                    </li>
                                                );
                                            } else {
                                                return (
                                                    <li className="mt-list-item">
                                                        <div className="list-icon-container">
                                                            <i className="icon-close"></i>
                                                        </div>
                                                        <div className="list-datetime"> {item.total_unlinked_photo} / {item.total_photos} </div>
                                                        <div className="list-item-content">
                                                            <h3 className="uppercase">
                                                                <a href="javascript:;">{item.municipality_name} </a>
                                                            </h3>
                                                        </div>
                                                    </li>
                                                );
                                            }
                                        })
                                    }


                                </ul>
                            </div>
                        </div>
                    </div>

                    <div className="col-md-8">
                        <div className="portlet light portlet-fit bordered" id="pending-photo-portlet">
                            <div className="portlet-body">


                                <div className="row" style={{ marginBottom: "10px" }}>
                                    <div className="col-md-2 col-md-offset-7">
                                        <select onChange={this.loadData} id="select-page-size" className="form-control form-filter input-sm" >
                                            <option value="10" selected="selected">Show 10</option>
                                            <option value="25">Show 25</option>
                                            <option value="50">Show 50</option>
                                            <option value="100">Show 100</option>
                                            <option value="200">Show 200</option>
                                        </select>
                                    </div>
                                    <div className="col-md-3">
                                        <select onChange={this.loadData} id="select-photo-group" className="form-control form-filter input-sm" >
                                            <option value="FOR_TAGGING" selected="selected">For Tagging</option>
                                            <option value="FOR_CROPPING">For Cropping</option>
                                            <option value="TAGGED">Tagged Photos</option>
                                            <option value="NOT_FOUND">Not Found</option>

                                        </select>
                                    </div>
                                </div>
                                <div className="mt-element-list">
                                    <div className="mt-list-head list-news ext-1 font-white bg-grey-gallery">
                                        <div className="list-head-title-container">
                                            <h3 className="list-title">Pending Photos <span>for tagging</span></h3>
                                        </div>
                                        <div className="list-count pull-right bg-red">{self.state.remainingPhotos}</div>
                                    </div>
                                    <div className="mt-list-container list-news ext-1">
                                        <ul>
                                            {
                                                this.state.items.map(function (item, index) {

                                                    let imgUrl = Routing.generate("ajax_get_field_upload_photo_v2", { id: item.id });
                                                    let imgUrlTagged = window.imgUrl + 3 + '_' + item.generated_id_no + "?" + new Date().getTime();
                                                    let photoGroup = $('#pending-photo-portlet #select-photo-group').val();

                                                    if (photoGroup == "TAGGED") {
                                                        return (
                                                            <li key={"pending" + index} className="mt-list-item" onClick={self.openCropTaggedModal.bind(self, item)}>
                                                                <div className="list-icon-container">
                                                                    <a href="javascript:;">
                                                                        <i className="fa fa-angle-right"></i>
                                                                    </a>
                                                                </div>
                                                                <div className="list-thumb">
                                                                    <a href="javascript:;">
                                                                        <img alt="" src={imgUrlTagged} />
                                                                    </a>
                                                                </div>
                                                                <div className="list-datetime bold uppercase font-red"> {item.barangay_name}, {item.municipality_name} </div>
                                                                <div className="list-item-content">
                                                                    <h3 className="uppercase">
                                                                        <a href="javascript:;">{item.display_name}</a>
                                                                    </h3>
                                                                    <p><em><small>Upload Date :  {item.created_at}</small></em></p>
                                                                </div>
                                                            </li>
                                                        );
                                                    } else {
                                                        return (
                                                            <li key={"pending" + index} className="mt-list-item" onClick={self.openTaggingModal.bind(self, item)}>
                                                                <div className="list-icon-container">
                                                                    <a href="javascript:;">
                                                                        <i className="fa fa-angle-right"></i>
                                                                    </a>
                                                                </div>
                                                                <div className="list-thumb">
                                                                    <a href="javascript:;">
                                                                        <img alt="" src={imgUrl} />
                                                                    </a>
                                                                </div>
                                                                <div className="list-datetime bold uppercase font-red"> {item.barangay_name}, {item.municipality_name} </div>
                                                                <div className="list-item-content">
                                                                    <h3 className="uppercase">
                                                                        <a href="javascript:;">{item.file_display_name}</a>
                                                                    </h3>
                                                                    <p><em><small>Upload Date :  {item.created_at}</small></em></p>
                                                                </div>
                                                            </li>
                                                        );
                                                    }

                                                })
                                            }

                                            {
                                                // this.state.items.length == 0 ? (
                                                //     <li className="mt-list-item" >
                                                //         <div className="list-item-content">
                                                //             <h3 className="uppercase text-center" style={{ marginTop: "20px" }}>
                                                //                 Opps! Ubos na po...Subokan po sa ibang barangay.
                                                //             </h3>
                                                //         </div>
                                                //     </li>
                                                // ) : ""
                                            }

                                        </ul>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>


            </div>
        )
    }
});

setTimeout(function () {
    ReactDOM.render(
        <PhotoUpload />,
        document.getElementById('page-container')
    );
}, 500);
