import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { Fragment } from 'react';
import { __ } from '@wordpress/i18n';

function Edit({ attributes, setAttributes }) {
    const blockProps = useBlockProps({className: 'jobly-registration-section'});
    const { candidate_username, candidate_email, candidate_pass, candidate_confirm_pass } = attributes;
    const { employer_username, employer_email, employer_pass, employer_confirm_pass } = attributes;

    return (
        <Fragment>

            <InspectorControls>

                <PanelBody title={__('Candidate Form', 'jobly')}>
                    <TextControl
                        label={__('Username', 'jobly')}
                        type="text"
                        value={candidate_username}
                        placeholder={__('candidate', 'jobly')}
                        onChange={(value) => setAttributes({candidate_username: value})}
                    />
                    <TextControl
                        label={__('Email', 'jobly')}
                        type="email"
                        value={candidate_email}
                        placeholder={__('candidate@example.com', 'jobly')}
                        onChange={(value) => setAttributes({candidate_email: value})}
                    />
                    <TextControl
                        label={__('Password', 'jobly')}
                        type="password"
                        value={candidate_pass}
                        placeholder={__('demo', 'jobly')}
                        onChange={(value) => setAttributes({candidate_pass: value})}
                    />
                    <TextControl
                        label={__('Confirm Password', 'jobly')}
                        type="password"
                        value={candidate_confirm_pass}
                        placeholder={__('demo', 'jobly')}
                        onChange={(value) => setAttributes({candidate_confirm_pass: value})}
                    />
                </PanelBody>

                <PanelBody title={__('Employer Form', 'jobly')}>
                    <TextControl
                        label={__('Username', 'jobly')}
                        type="text"
                        value={employer_username}
                        placeholder={__('employer', 'jobly')}
                        onChange={(value) => setAttributes({candidate_username: value})}
                    />
                    <TextControl
                        label={__('Email', 'jobly')}
                        type="email"
                        value={employer_email}
                        placeholder={__('employer@example.com', 'jobly')}
                        onChange={(value) => setAttributes({candidate_email: value})}
                    />
                    <TextControl
                        label={__('Password', 'jobly')}
                        type="password"
                        value={employer_pass}
                        placeholder={__('demo', 'jobly')}
                        onChange={(value) => setAttributes({candidate_pass: value})}
                    />
                    <TextControl
                        label={__('Confirm Password', 'jobly')}
                        type="password"
                        value={employer_confirm_pass}
                        placeholder={__('demo', 'jobly')}
                        onChange={(value) => setAttributes({candidate_confirm_pass: value})}
                    />
                </PanelBody>

            </InspectorControls>


            <div {...blockProps}>
                <div className="user-data-form">

                    <div className="text-center">
                        <h2>{__('Create Account', 'jobly')}</h2>
                    </div>

                    <div className="form-wrapper m-auto">

                        <ul className="nav nav-tabs border-0 w-100 mt-30" role="tablist">
                            <li className="nav-item" role="presentation">
                                <button className="nav-link" data-bs-toggle="tab" data-bs-target="#fc1" role="tab" aria-selected="false">{__('Candidates', 'jobly')}</button>
                            </li>
                            <li className="nav-item" role="presentation">
                                <button className="nav-link active" data-bs-toggle="tab" data-bs-target="#fc2" role="tab" aria-selected="true">{__('Employer', 'jobly')}</button>
                            </li>
                        </ul>

                        <div className="tab-content mt-40">

                            <div className="tab-pane fade" role="tabpanel" id="fc1">
                                <form>
                                    <div className="row">
                                        <div className="col-12">
                                            <div className="input-group-meta position-relative mb-25">
                                                <label>{__('Name*', 'jobly')}</label>
                                                <input type="text" placeholder={__('Rashed Kabir', 'jobly')}
                                                       value={candidate_username} readOnly/>
                                            </div>
                                        </div>
                                        <div className="col-12">
                                            <div className="input-group-meta position-relative mb-25">
                                                <label>{__('Email*', 'jobly')}</label>
                                                <input type="email" placeholder="rshdkabir@gmail.com"
                                                       value={candidate_email} readOnly/>
                                            </div>
                                        </div>
                                        <div className="col-12">
                                            <div className="input-group-meta position-relative mb-20">
                                                <label>{__('Password*', 'jobly')}</label>
                                                <input type="password" placeholder={__('Enter Password', 'jobly')}
                                                       value={candidate_pass} readOnly/>
                                            </div>
                                        </div>
                                        <div className="col-12">
                                            <div className="input-group-meta position-relative mb-20">
                                                <label>{__('Confirm Password*', 'jobly')}</label>
                                                <input type="password" placeholder={__('Confirm Password', 'jobly')}
                                                       value={candidate_confirm_pass} readOnly/>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div className="tab-pane fade active show" role="tabpanel" id="fc2">
                                <form>
                                    <div className="row">
                                        <div className="col-12">
                                            <div className="input-group-meta position-relative mb-25">
                                                <label>{__('Name*', 'jobly')}</label>
                                                <input type="text" placeholder="Zubayer Hasan" value={employer_username}
                                                       readOnly/>
                                            </div>
                                        </div>
                                        <div className="col-12">
                                            <div className="input-group-meta position-relative mb-25">
                                                <label>{__('Email*', 'jobly')}</label>
                                                <input type="email" placeholder="zubayerhasan@gmail.com"
                                                       value={employer_email} readOnly/>
                                            </div>
                                        </div>
                                        <div className="col-12">
                                            <div className="input-group-meta position-relative mb-20">
                                                <label>{__('Password*', 'jobly')}</label>
                                                <input type="password" placeholder={__('Enter Password', 'jobly')}
                                                       value={employer_pass} readOnly/>
                                            </div>
                                        </div>
                                        <div className="col-12">
                                            <div className="input-group-meta position-relative mb-20">
                                                <label>{__('Confirm Password*', 'jobly')}</label>
                                                <input type="password" placeholder={__('Confirm Password', 'jobly')}
                                                       value={employer_confirm_pass} readOnly/>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </Fragment>
    );
}

export default Edit;