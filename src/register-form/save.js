import { useBlockProps } from '@wordpress/block-editor';

function Save({ attributes }) {
    const blockProps = useBlockProps.save({className: 'registration-section position-relative pt-100 lg-pt-80 pb-150 lg-pb-80'});
    const { candidate_username, candidate_email, candidate_pass, candidate_confirm_pass } = attributes;


    return (
        <div {...blockProps}>
            <div className="container">
                <div className="user-data-form">
                    <div className="text-center">
                        <h2>Create Account</h2>
                    </div>
                    <div className="form-wrapper m-auto">
                        <ul className="nav nav-tabs border-0 w-100 mt-30" role="tablist">
                            <li className="nav-item" role="presentation">
                                <button className="nav-link active" data-bs-toggle="tab" data-bs-target="#fc1"
                                        role="tab">Candidates
                                </button>
                            </li>
                            <li className="nav-item" role="presentation">
                                <button className="nav-link" data-bs-toggle="tab" data-bs-target="#fc2"
                                        role="tab">Employer
                                </button>
                            </li>
                        </ul>
                        <div className="tab-content mt-40">
                            <div className="tab-pane fade show active" role="tabpanel" id="fc1">
                                <form action="#">
                                    <div className="row">
                                        <div className="col-12">
                                            <div className="input-group-meta position-relative mb-25">
                                                <label>Name*</label>
                                                <input type="text" placeholder="Rashed Kabir"/>
                                            </div>
                                        </div>
                                        <div className="col-12">
                                            <div className="input-group-meta position-relative mb-25">
                                                <label>Email*</label>
                                                <input type="email" placeholder="rshdkabir@gmail.com"/>
                                            </div>
                                        </div>
                                        <div className="col-12">
                                            <div className="input-group-meta position-relative mb-20">
                                                <label>Password*</label>
                                                <input type="password" placeholder="Enter Password"
                                                       className="pass_log_id"/>
                                                <span className="placeholder_icon"><span className="passVicon"><img
                                                    src="images/icon/icon_60.svg" alt=""/></span></span>
                                            </div>
                                        </div>
                                        <div className="col-12">
                                            <div
                                                className="agreement-checkbox d-flex justify-content-between align-items-center">
                                                <div>
                                                    <input type="checkbox" id="remember"/>
                                                    <label htmlFor="remember">By hitting the "Register" button, you
                                                        agree to the <a href="#">Terms conditions</a> &amp; <a
                                                            href="#">Privacy Policy</a></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div className="col-12">
                                            <button className="btn-eleven fw-500 tran3s d-block mt-20">Register
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div className="tab-pane fade" role="tabpanel" id="fc2">
                                <form action="#">
                                    <div className="row">
                                        <div className="col-12">
                                            <div className="input-group-meta position-relative mb-25">
                                                <label>Name*</label>
                                                <input type="text" placeholder="Zubayer Hasan"/>
                                            </div>
                                        </div>
                                        <div className="col-12">
                                            <div className="input-group-meta position-relative mb-25">
                                                <label>Email*</label>
                                                <input type="email" placeholder="zubayerhasan@gmail.com"/>
                                            </div>
                                        </div>
                                        <div className="col-12">
                                            <div className="input-group-meta position-relative mb-20">
                                                <label>Password*</label>
                                                <input type="password" placeholder="Enter Password"
                                                       className="pass_log_id"/>
                                                <span className="placeholder_icon"><span className="passVicon"><img
                                                    src="images/icon/icon_60.svg" alt=""/></span></span>
                                            </div>
                                        </div>
                                        <div className="col-12">
                                            <div
                                                className="agreement-checkbox d-flex justify-content-between align-items-center">
                                                <div>
                                                    <input type="checkbox" id="remember"/>
                                                    <label htmlFor="remember">By hitting the "Register" button, you
                                                        agree to the <a href="#">Terms conditions</a> &amp; <a
                                                            href="#">Privacy Policy</a></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div className="col-12">
                                            <button className="btn-eleven fw-500 tran3s d-block mt-20">Register
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div className="d-flex align-items-center mt-30 mb-10">
                            <div className="line"></div>
                            <span className="pe-3 ps-3">OR</span>
                            <div className="line"></div>
                        </div>
                        <div className="row">
                            <div className="col-sm-6">
                                <a href="#"
                                   className="social-use-btn d-flex align-items-center justify-content-center tran3s w-100 mt-10">
                                    <img src="images/icon/google.png" alt=""/>
                                    <span className="ps-2">Signup with Google</span>
                                </a>
                            </div>
                            <div className="col-sm-6">
                                <a href="#"
                                   className="social-use-btn d-flex align-items-center justify-content-center tran3s w-100 mt-10">
                                    <img src="images/icon/facebook.png" alt=""/>
                                    <span className="ps-2">Signup with Facebook</span>
                                </a>
                            </div>
                        </div>
                        <p className="text-center mt-10">Have an account? <a href="#" className="fw-500" data-bs-toggle="modal" data-bs-target="#loginModal">Sign In</a></p>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default Save;