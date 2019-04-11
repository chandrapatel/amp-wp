/**
 * This is a 1:1 copy of the InserterListItem component in @wordpress/block-editor.
 *
 * It is included here because the component is not exported to the public by that package.
 */

/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import { BlockIcon } from '@wordpress/block-editor';

function InserterListItem( {
	icon,
	hasChildBlocksWithInserterSupport,
	onClick,
	isDisabled,
	title,
	className,
	...props
} ) {
	const itemIconStyle = icon ? {
		backgroundColor: icon.background,
		color: icon.foreground,
	} : {};
	const itemIconStackStyle = icon && icon.shadowColor ? {
		backgroundColor: icon.shadowColor,
	} : {};

	return (
		<li className="editor-block-types-list__list-item block-editor-block-types-list__list-item">
			<button
				className={
					classnames(
						'editor-block-types-list__item block-editor-block-types-list__item',
						className,
						{
							'editor-block-types-list__item-has-children block-editor-block-types-list__item-has-children':
								hasChildBlocksWithInserterSupport,
						}
					)
				}
				onClick={ ( event ) => {
					event.preventDefault();
					onClick();
				} }
				disabled={ isDisabled }
				aria-label={ title } // Fix for IE11 and JAWS 2018.
				{ ...props }
			>
				<span
					className="editor-block-types-list__item-icon block-editor-block-types-list__item-icon"
					style={ itemIconStyle }
				>
					<BlockIcon icon={ icon } showColors />
					{ hasChildBlocksWithInserterSupport &&
						<span
							className="editor-block-types-list__item-icon-stack block-editor-block-types-list__item-icon-stack"
							style={ itemIconStackStyle }
						/>
					}
				</span>
				<span className="editor-block-types-list__item-title block-editor-block-types-list__item-title">
					{ title }
				</span>
			</button>
		</li>
	);
}

export default InserterListItem;
