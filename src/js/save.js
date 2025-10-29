import { useBlockProps } from '@wordpress/block-editor';

export default function save({ attributes }) {
	const { layout, tags = "[]" } = attributes; // 👈 tags ergänzen

	return (
		<div
			{...useBlockProps.save({
				className: `ud-personal-card ud-layout-${layout}`,
				"data-tags": tags, // 👈 zwingend nötig für Persistenz

			})}
		/>
	);
}
