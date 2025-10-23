import {useBlockProps, InspectorControls} from '@wordpress/block-editor';
import {PanelBody, TextControl} from '@wordpress/components';
import {Fragment} from 'react';
import {__} from '@wordpress/i18n';

function Edit({attributes, setAttributes}) {
    const blockProps = useBlockProps({className: 'jobus-registration-section'});
    const {candidate_username, candidate_email, candidate_pass, candidate_confirm_pass} = attributes;
    const {employer_username, employer_email, employer_pass, employer_confirm_pass} = attributes;

    return (
        <Fragment>

            <InspectorControls>

                {/*================ Candidate Form ============== */}
                <PanelBody title={__('Candidate Form', 'jobus')} initialOpen={true}>
                    <TextControl
                        label={__('Placeholder Username', 'jobus')}
                        value={candidate_username}
                        placeholder={__('candidate', 'jobus')}
                        onChange={(value) => setAttributes({candidate_username: value})}
                    />
                    <TextControl
                        label={__('Placeholder Email', 'jobus')}
                        type="email"
                        value={candidate_email}
                        placeholder={__('candidate@example.com', 'jobus')}
                        onChange={(value) => setAttributes({candidate_email: value})}
                    />
                    <TextControl
                        label={__('Placeholder Password', 'jobus')}
                        value={candidate_pass}
                        placeholder={__('demo', 'jobus')}
                        onChange={(value) => setAttributes({candidate_pass: value})}
                    />
                    <TextControl
                        label={__('Placeholder Confirm Password', 'jobus')}
                        value={candidate_confirm_pass}
                        placeholder={__('demo', 'jobus')}
                        onChange={(value) => setAttributes({candidate_confirm_pass: value})}
                    />
                </PanelBody>

                {/*================ Employer Form ============== */}
                <PanelBody title={__('Employer Form', 'jobus')} initialOpen={false}>
                    <TextControl
                        label={__('Placeholder Username', 'jobus')}
                        type="text"
                        value={employer_username}
                        placeholder={__('employer', 'jobus')}
                        onChange={(value) => setAttributes({employer_username: value})}
                    />
                    <TextControl
                        label={__('Placeholder Email', 'jobus')}
                        type="email"
                        value={employer_email}
                        placeholder={__('employer@example.com', 'jobus')}
                        onChange={(value) => setAttributes({employer_email: value})}
                    />
                    <TextControl
                        label={__('Placeholder Password', 'jobus')}
                        value={employer_pass}
                        placeholder={__('demo', 'jobus')}
                        onChange={(value) => setAttributes({employer_pass: value})}
                    />
                    <TextControl
                        label={__('Placeholder Confirm Password', 'jobus')}
                        value={employer_confirm_pass}
                        placeholder={__('demo', 'jobus')}
                        onChange={(value) => setAttributes({employer_confirm_pass: value})}
                    />
                </PanelBody>

            </InspectorControls>


            <div {...blockProps}>
                <div className="user-data-form">

                    <div className="jbs-text-center">
                        <h2>{__('Create Account', 'jobus')}</h2>
                    </div>

                    <div className="form-wrapper jbs-m-auto">

                        <ul className="nav nav-tabs jbs-border-0 jbs-w-100" role="tablist">
                            <li className="nav-item" role="presentation">
                                <button className="nav-link active" data-jbs-toggle="tab" data-jbs-target="#fc1" role="tab" aria-selected="false">{__('Candidates', 'jobus')}</button>
                            </li>
                            <li className="nav-item" role="presentation">
                                <button className="nav-link" data-jbs-toggle="tab" data-jbs-target="#fc2" role="tab" aria-selected="true">{__('Employer', 'jobus')}</button>
                            </li>
                        </ul>

                        <div className="tab-content">

                            {/*============= Candidate Form ===================*/}
                            <div className="tab-pane jbs-fade active show" role="tabpanel" id="fc1">
                                <form method="post">
                                    <div className="jbs-row">
                                        <div className="jbs-col-12">
                                            <div className="input-group-meta jbs-position-relative">
                                                <label htmlFor="candidate_username">{__('Name*', 'jobus')}</label>
                                                <input
                                                    type="text"
                                                    name="candidate_username"
                                                    id="candidate_username"
                                                    placeholder={candidate_username}
                                                    value={candidate_username}
                                                    onChange={(event) => setAttributes({candidate_username: event.target.value})}
                                                />
                                            </div>
                                        </div>
                                        <div className="jbs-col-12">
                                            <div className="input-group-meta jbs-position-relative">
                                                <label htmlFor="candidate_email">{__('Email*', 'jobus')}</label>
                                                <input
                                                    type="email"
                                                    name="candidate_email"
                                                    id="candidate_email"
                                                    placeholder={candidate_email}
                                                    value={candidate_email}
                                                    onChange={(event) => setAttributes({candidate_email: event.target.value})}
                                                />
                                            </div>
                                        </div>
                                        <div className="jbs-col-12">
                                            <div className="input-group-meta jbs-position-relative">
                                                <label htmlFor="candidate_pass">{__('Password*', 'jobus')}</label>
                                                <input
                                                    name="candidate_pass"
                                                    id="candidate_pass"
                                                    placeholder={candidate_pass}
                                                    value={candidate_pass}
                                                    onChange={(event) => setAttributes({candidate_pass: event.target.value})}
                                                />
                                            </div>
                                        </div>
                                        <div className="jbs-col-12">
                                            <div className="input-group-meta jbs-position-relative">
                                                <label htmlFor="candidate_confirm_pass">{__('Confirm Password*', 'jobus')}</label>
                                                <input
                                                    name="candidate_confirm_pass"
                                                    id="candidate_confirm_pass"
                                                    placeholder={candidate_confirm_pass}
                                                    value={candidate_confirm_pass}
                                                    onChange={(event) => setAttributes({candidate_confirm_pass: event.target.value})}
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            {/*============= Employer Form ===================*/}
                            <div className="tab-pane jbs-fade" role="tabpanel" id="fc2">
                                <form method="post">
                                    <div className="jbs-row">
                                        <div className="jbs-col-12">
                                            <div className="input-group-meta jbs-position-relative">
                                                <label htmlFor="employer_username">{__('Name*', 'jobus')}</label>
                                                <input
                                                    type="text"
                                                    name="employer_username"
                                                    id="employer_username"
                                                    placeholder={employer_username}
                                                    value={employer_username}
                                                    onChange={(event) => setAttributes({employer_username: event.target.value})}
                                                />
                                            </div>
                                        </div>
                                        <div className="jbs-col-12">
                                            <div className="input-group-meta jbs-position-relative">
                                                <label htmlFor="employer_email">{__('Email*', 'jobus')}</label>
                                                <input
                                                    type="email"
                                                    name="employer_email"
                                                    id="employer_email"
                                                    placeholder={employer_email}
                                                    value={employer_email}
                                                    onChange={(event) => setAttributes({employer_email: event.target.value})}
                                                />
                                            </div>
                                        </div>
                                        <div className="jbs-col-12">
                                            <div className="input-group-meta jbs-position-relative">
                                                <label htmlFor="employer_pass">{__('Password*', 'jobus')}</label>
                                                <input
                                                    name="employer_pass"
                                                    id="employer_pass"
                                                    placeholder={employer_pass}
                                                    value={employer_pass}
                                                    onChange={(event) => setAttributes({employer_pass: event.target.value})}
                                                />
                                            </div>
                                        </div>
                                        <div className="jbs-col-12">
                                            <div className="input-group-meta jbs-position-relative">
                                                <label htmlFor="employer_confirm_pass">{__('Confirm Password*', 'jobus')}</label>
                                                <input
                                                    name="employer_confirm_pass"
                                                    id="employer_confirm_pass"
                                                    placeholder={employer_confirm_pass}
                                                    value={employer_confirm_pass}
                                                    onChange={(event) => setAttributes({employer_confirm_pass: event.target.value})}
                                                />
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