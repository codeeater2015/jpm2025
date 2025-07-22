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
  
    render: function () {
        return (
            <div>
                <div className="row">
                    <div className="col-md-12">
                        <h1>Download Progress</h1>
                    </div>
                </div>
                <div className="row">
                    <div className="col-md-8">
                        <h3>Active Event : Barangay 1 @ May 1, 2025</h3>
                        <div className="text-center" style={{ backgroundColor:"#48c27f", padding:"10px", fontSize : "100px", fontWeight: "bold" }}><strong>1,400 </strong><small style={{ fontSize: "25px"}}>downloaded</small></div>
                        <br/>
                        <div>
                            <table className="table  table-bordered table-condensed">
                                
                                <tbody>
                                    <tr>
                                        <td className="text-center" style={{ fontSize:"50px"}}><strong>1234</strong></td>
                                        <td className="text-left" style={{ fontSize:"50px"}} ><strong>BARANGAY 1 , ROXAS</strong></td>
                                    </tr>
                                    <tr>
                                        <td className="text-center" style={{ fontSize:"50px"}}><strong>1234</strong></td>
                                        <td className="text-left" style={{ fontSize:"50px"}} ><strong>BARANGAY 1 , ROXAS</strong></td>
                                    </tr>
                                    <tr>
                                        <td className="text-center" style={{ fontSize:"50px"}}><strong>1234</strong></td>
                                        <td className="text-left" style={{ fontSize:"50px"}} ><strong>BARANGAY 1 , ROXAS</strong></td>
                                    </tr>
                                    <tr>
                                        <td className="text-center" style={{ fontSize:"50px"}}><strong>1234</strong></td>
                                        <td className="text-left" style={{ fontSize:"50px"}} ><strong>BARANGAY 1 , ROXAS</strong></td>
                                    </tr>
                                    <tr>
                                        <td className="text-center" style={{ fontSize:"50px"}}><strong>1234</strong></td>
                                        <td className="text-left" style={{ fontSize:"50px"}} ><strong>BARANGAY 1 , ROXAS</strong></td>
                                    </tr>
                                    <tr>
                                        <td className="text-center" style={{ fontSize:"50px"}}><strong>1234</strong></td>
                                        <td className="text-left" style={{ fontSize:"50px"}} ><strong>BARANGAY 1 , ROXAS</strong></td>
                                    </tr>
                                    <tr>
                                        <td className="text-center" style={{ fontSize:"50px"}}><strong>1234</strong></td>
                                        <td className="text-left" style={{ fontSize:"50px"}} ><strong>BARANGAY 1 , ROXAS</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div className="col-md-4 text-center">
                        <h3>Summary</h3>
                        <div className="text-center" style={{ backgroundColor:"#d9524b", padding:"10px", fontSize : "50px", fontWeight: "bold" }}><strong>10,400 </strong><small style={{ fontSize: "15px"}}>downloaded</small></div>
                        <table className="table table-bordered ">
                            <tbody>
                                <tr>
                                    <td className="text-center" style={{ fontSize : "20px"}}><strong>Abaroan : 1234</strong></td>
                                </tr>
                                <tr>
                                    <td className="text-center" style={{ fontSize : "20px"}}><strong>Abaroan : 1234</strong></td>
                                </tr>
                                <tr>
                                    <td className="text-center" style={{ fontSize : "20px"}}><strong>Abaroan : 1234</strong></td>
                                </tr>
                                <tr>
                                    <td className="text-center" style={{ fontSize : "20px"}}><strong>Abaroan : 1234</strong></td>
                                </tr>
                                <tr>
                                    <td className="text-center" style={{ fontSize : "20px"}}><strong>Abaroan : 1234</strong></td>
                                </tr>
                                <tr>
                                    <td className="text-center" style={{ fontSize : "20px"}}><strong>Abaroan : 1234</strong></td>
                                </tr>
                                <tr>
                                    <td className="text-center" style={{ fontSize : "20px"}}><strong>Abaroan : 1234</strong></td>
                                </tr>
                                <tr>
                                    <td className="text-center" style={{ fontSize : "20px"}}><strong>Abaroan : 1234</strong></td>
                                </tr>
                                <tr>
                                    <td className="text-center" style={{ fontSize : "20px"}}><strong>Abaroan : 1234</strong></td>
                                </tr>
                                <tr>
                                    <td className="text-center" style={{ fontSize : "20px"}}><strong>Abaroan : 1234</strong></td>
                                </tr>
                                <tr>
                                    <td className="text-center" style={{ fontSize : "20px"}}><strong>Abaroan : 1234</strong></td>
                                </tr>
                                <tr>
                                    <td className="text-center" style={{ fontSize : "20px"}}><strong>Abaroan : 1234</strong></td>
                                </tr>
                                <tr>
                                    <td className="text-center" style={{ fontSize : "20px"}}><strong>Abaroan : 1234</strong></td>
                                </tr>
                                <tr>
                                    <td className="text-center" style={{ fontSize : "20px"}}><strong>Abaroan : 1234</strong></td>
                                </tr>
                                <tr>
                                    <td className="text-center" style={{ fontSize : "20px"}}><strong>Abaroan : 1234</strong></td>
                                </tr>
                            </tbody>
                        </table>
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
