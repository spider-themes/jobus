import { useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

function Edit({ attributes, setAttributes }) {
    const { candidate_username, candidate_email, candidate_pass, candidate_confirm_pass } = attributes;
    const blockProps = useBlockProps();

    return (
        <div {...blockProps}>
            <h2>{__('Create Account', 'jobly')}</h2>
            <form>
                <label>{__('Name', 'jobly')}*</label>
                <input
                    type="text"
                    value={candidate_username}
                    onChange={(e) => setAttributes({ candidate_username: e.target.value })}
                    placeholder={__('Rashed Kabir', 'jobly')}
                />
                <label>{__('Email', 'jobly')}*</label>
                <input
                    type="email"
                    value={candidate_email}
                    onChange={(e) => setAttributes({ candidate_email: e.target.value })}
                    placeholder="rshdkabir@gmail.com"
                />
                <label>{__('Password', 'jobly')}*</label>
                <input
                    type="password"
                    value={candidate_pass}
                    onChange={(e) => setAttributes({ candidate_pass: e.target.value })}
                    placeholder={__('Enter Password', 'jobly')}
                />
                <label>{__('Confirm Password', 'jobly')}*</label>
                <input
                    type="password"
                    value={candidate_confirm_pass}
                    onChange={(e) => setAttributes({ candidate_confirm_pass: e.target.value })}
                    placeholder={__('Confirm Password', 'jobly')}
                />
                <div>
                    <input type="checkbox" id="remember" required />
                    <label htmlFor="remember">
                        {__('By hitting the "Register" button, you agree to the', 'jobly')}
                        <a href="#">{__('Terms conditions', 'jobly')}</a> & <a href="#">{__('Privacy Policy', 'jobly')}</a>
                    </label>
                </div>
                <button className="btn-eleven">{__('Register', 'jobly')}</button>
            </form>
        </div>
    );
}

export default Edit;