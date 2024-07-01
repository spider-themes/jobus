import {useBlockProps} from "@wordpress/block-editor";

function Save({attributes}) {

    const blockProps = useBlockProps.save({
        className: 'video-post d-flex align-items-center justify-content-center mb-50',
        style: {
            backgroundImage: `url(${attributes.backgroundImage})`,
            backgroundRepeat: 'no-repeat',
            backgroundSize: 'cover',
            backgroundPosition: 'center center'
        },
    });

    return (
        <div {...blockProps}>
            <a href={attributes.videoUrl} className="fancybox rounded-circle video-icon tran3s text-center" style={
                {
                    backgroundColor: attributes.icon_bg_color,
                }
            }>
                <i className="bi bi-play-fill" style={{color: attributes.icon_color}}></i>
            </a>
        </div>
    );

}

export default Save;