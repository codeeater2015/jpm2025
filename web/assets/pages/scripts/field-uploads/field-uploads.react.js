var ProjectEventComponent = React.createClass({

    getInitialState: function () {
        return {
            form: {
                data: {
                    municipalityNo: "",
                    barangayNo: "",
                },
                errors: []
            },
            items: [],
            showCropModal: false,
            targetProVoterId: null,
            targetIdNo: null,
            remainingPhotos: 0,
            breakdownList: []
        };
    },

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
        this.initSelect2();
        this.loadData();
    },


    loadBreakdown: function () {
        var self = this;
        let municipalityNo = self.state.form.data.municipalityNo;

        let url = "";

        if (municipalityNo != "" && municipalityNo != null) {
            url = Routing.generate("ajax_m_get_field_photos_remaining_per_barangay", {
                municipalityNo: municipalityNo
            });
        } else {
            url = Routing.generate("ajax_m_get_field_photos_remaining_per_municipality");
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

    loadRemainingPhotoCounter: function () {
        var self = this;

        let url = Routing.generate("ajax_m_get_field_photos_remaining", {
            municipalityNo: self.state.form.data.municipalityNo,
            brgyNo: self.state.form.data.barangayNo
        });

        self.requestHierarchyData = $.ajax({
            url: url,
            type: "GET"
        }).done(function (res) {
            console.log("pending photos counter has been received.");
            console.log(res);
            self.setState({ remainingPhotos: res.total_remaining_photos }, self.loadBreakdown);
        });
    },

    loadData: function () {
        var self = this;

        let url = Routing.generate("ajax_m_get_field_photos_for_cropping", {
            municipalityNo: self.state.form.data.municipalityNo,
            brgyNo: self.state.form.data.barangayNo,
            pageSize: $('#pending-photo-portlet #select-page-size').val()
        });

        self.requestHierarchyData = $.ajax({
            url: url,
            type: "GET"
        }).done(function (res) {
            console.log("pending photos has been received.");
            console.log(res.length);

            self.setState({ items: res }, self.loadRemainingPhotoCounter);
        });
    },


    initSelect2: function () {
        var self = this;

        $("#pending-photo-portlet #municipality_select2").select2({
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

        $("#pending-photo-portlet #barangay_select2").select2({
            casesentitive: false,
            placeholder: "Select Barangay",
            allowClear: true,
            delay: 1500,
            width: '100%',
            containerCssClass: ':all:',
            ajax: {
                url: Routing.generate('ajax_select2_barangay'),
                data: function (params) {
                    return {
                        searchText: params.term,
                        municipalityNo: $("#pending-photo-portlet #municipality_select2").val(),
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

        $("#pending-photo-portlet #municipality_select2").on("change", function () {
            self.setFormPropValue('municipalityNo', $(this).val());

        });

        $("#pending-photo-portlet #barangay_select2").on("change", function () {
            self.setFormPropValue('barangayNo', $(this).val());
        });
    },

    setFormPropValue: function (field, value) {
        var form = this.state.form;
        form.data[field] = value;
        this.setState({ form: form }, this.loadData);
    },

    closeCropModal: function () {
        this.setState({ showCropModal: false });
    },

    openCropModal: function (item) {
        console.log("target item", item);
        this.setState({
            targetProVoterId: item.pro_voter_id,
            targetIdNo: item.generated_id_no,
            showCropModal: true
        });
    },
    onCropSuccess: function () {
        let self = this;

        setTimeout(function () {
            self.setState({
                targetProVoterId: null,
                targetIdNo: null
            }, self.loadData);
        },
            1000);
    },
    render: function () {
        let self = this;

        return (
            <div className="row">

                {
                    this.state.showCropModal &&
                    (
                        <VoterCropModal
                            proId={3}
                            proVoterId={this.state.targetProVoterId}
                            generatedIdNo={this.state.targetIdNo}
                            show={this.state.showCropModal}
                            onHide={this.closeCropModal}
                            onSuccess={this.onCropSuccess}
                        />
                    )
                }

                <div className="col-md-10">
                    <div className="portlet box green-seagreen" id="pending-photo-portlet">
                        <div className="portlet-title ">
                            <div className="caption">
                                <i className="fa fa-gift"></i>Field Photo Uploads
                            </div>
                            <div className="tools">
                                <a href="" className="fullscreen"> </a>
                                <a href="javascript:;" className="collapse"> </a>
                            </div>
                        </div>
                        <div className="portlet-body">
                            <div className="scroller" data-rail-visible="1" data-rail-color="yellow" data-handle-color="#a1b2bd">
                                <div className="row">
                                    <div className="col-md-3">
                                        <select id="municipality_select2" className="form-control form-filter input-sm" >
                                        </select>
                                    </div>
                                    <div className="col-md-3">
                                        <select id="barangay_select2" className="form-control form-filter input-sm" >
                                        </select>
                                    </div>
                                </div>
                                <br />

                                <div className="row">
                                    <div className="col-md-8">
                                        <div className="row" style={{ marginBottom: "10px" }}>
                                            <div className="col-md-2 col-md-offset-10">
                                                <select onChange={this.loadData} id="select-page-size" className="form-control form-filter input-sm" >
                                                    <option value="10" selected="selected">Show 10</option>
                                                    <option value="25">Show 25</option>
                                                    <option value="50">Show 50</option>
                                                    <option value="100">Show 100</option>
                                                    <option value="200">Show 200</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div className="mt-element-list">
                                            <div className="mt-list-head list-news ext-1 font-white bg-grey-gallery">
                                                <div className="list-head-title-container">
                                                    <h3 className="list-title">E-crop mo na ako!</h3>
                                                </div>
                                                <div className="list-count pull-right bg-red">{self.state.remainingPhotos}</div>
                                            </div>
                                            <div className="mt-list-container list-news ext-1">
                                                <ul>
                                                    {
                                                        this.state.items.map(function (item, index) {

                                                            return (
                                                                <li key={"pending" + index} className="mt-list-item" onClick={self.openCropModal.bind(self, item)}>
                                                                    <div className="list-icon-container">
                                                                        <a href="javascript:;">
                                                                            <i className="fa fa-angle-right"></i>
                                                                        </a>
                                                                    </div>
                                                                    {/* <div className="list-thumb">
                                                                        <a href="javascript:;">
                                                                            <img alt="" src={item.imgUrl} />
                                                                        </a>
                                                                    </div> */}
                                                                    <div className="list-datetime bold uppercase font-red"> {item.barangay_name}, {item.municipality_name} </div>
                                                                    <div className="list-item-content">
                                                                        <h3 className="uppercase">
                                                                            <a href="javascript:;">{item.voter_name}</a>
                                                                        </h3>
                                                                        <p><em><small>Photo taken :  {item.photo_at}</small></em></p>
                                                                    </div>
                                                                </li>
                                                            );
                                                        })
                                                    }

                                                    {
                                                        this.state.items.length == 0 ? (
                                                            <li className="mt-list-item" >
                                                                <div className="list-item-content">
                                                                    <h3 className="uppercase text-center" style={{ marginTop: "20px" }}>
                                                                        Opps! Ubos na po...Subokan po sa ibang barangay.
                                                                    </h3>
                                                                </div>
                                                            </li>
                                                        ) : ""
                                                    }

                                                </ul>
                                            </div>
                                        </div>

                                    </div>

                                    <div className="col-md-4">

                                        <div className="mt-element-list">
                                            <div className="mt-list-head list-simple ext-1 font-white bg-green-sharp">
                                                <div className="list-head-title-container">
                                                    <h3 className="list-title ">Pili lng mga Pre.</h3>
                                                </div>

                                            </div>
                                            <div className="mt-list-container list-simple ext-1">
                                                <ul>
                                                    {
                                                        self.state.breakdownList.map((item) => {
                                                            let difference = Number.parseInt(item.total_remaining_photos) - Number.parseInt(item.total_cropped);

                                                            if (difference == 0) {
                                                                return (
                                                                    <li className="mt-list-item done">
                                                                        <div className="list-icon-container">
                                                                            <i className="icon-check"></i>
                                                                        </div>
                                                                        <div className="list-datetime"> {item.total_uncropped} / {item.total_remaining_photos} </div>
                                                                        <div className="list-item-content">
                                                                            <h3 className="uppercase">
                                                                                <a href="javascript:;">{item.label_name} </a>
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
                                                                        <div className="list-datetime"> {item.total_uncropped} / {item.total_remaining_photos} </div>
                                                                        <div className="list-item-content">
                                                                            <h3 className="uppercase">
                                                                                <a href="javascript:;">{item.label_name} </a>
                                                                            </h3>
                                                                        </div>
                                                                    </li>
                                                                );
                                                            }
                                                        })
                                                    }
                                                    {/* <li className="mt-list-item">
                                                        <div className="list-icon-container">
                                                            <i className="icon-close"></i>
                                                        </div>
                                                        <div className="list-datetime">12</div>
                                                        <div className="list-item-content">
                                                            <h3 className="uppercase">
                                                                <a href="javascript:;">Balabac</a>
                                                            </h3>
                                                        </div>
                                                    </li> */}

                                                </ul>
                                            </div>
                                        </div>

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
        <ProjectEventComponent />,
        document.getElementById('page-container')
    );
}, 500);
