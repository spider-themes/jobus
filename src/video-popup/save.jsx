// jobly/blocks/video-popup/save.jsx
const Save = ({ attributes }) => {
    return (
        <div className="wp-block-jobly-video-popup">
            {/* Your custom markup here based on the attributes */}
            <div className="video-post d-flex align-items-center justify-content-center mb-50">
                <a
                    className="fancybox rounded-circle video-icon tran3s text-center"
                    data-fancybox=""
                    href={attributes.videoUrl}
                >
                    <i className="bi bi-play-fill"></i>
                </a>
            </div>
        </div>
    );
};

export default Save;