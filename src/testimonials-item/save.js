import {useBlockProps} from "@wordpress/block-editor";
import {__} from "@wordpress/i18n";

function Save ({attributes}) {

    const blockProps = useBlockProps.save({className: 'item'});
    const { authorName, location, reviewContents, rating, authorImage } = attributes;

    return (
        <div {...blockProps}>

            <div className="feedback-block-four ddddd">
                <div className="jbs-d-flex jbs-align-items-center">
                    <ul className="style-none jbs-d-flex rating">
                        {Array.from({length: rating}, (_, index) => (
                            <li key={index}><a href="#" tabIndex="0"><i className="bi bi-star-fill"></i></a></li>
                        ))}
                    </ul>
                    <div className="review-score"><span className="jbsfw-500 jbs-text-dark">{rating}</span> {__('out of 5', 'jobus')}</div>
                </div>
                <blockquote>{reviewContents}</blockquote>
                <div className="jbs-d-flex jbs-align-items-center">
                    {authorImage && <img src={authorImage} alt="Author" className="author-img jbs-rounded-circle"/>}
                    <div className="ms-3">
                        <div className="name jbs-fw-500 jbs-text-dark">{authorName}</div>
                        <span className="jbs-opacity-50">{location}</span>
                    </div>
                </div>
            </div>
        </div>
    );

}


export default Save;