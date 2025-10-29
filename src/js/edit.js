import { __ } from "@wordpress/i18n";
import {
	useBlockProps,
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
	RichText,
	PanelColorSettings,
	withColors,
} from "@wordpress/block-editor";

import { PanelBody, ToggleControl, TextControl, Button, FormTokenField } from "@wordpress/components";
import { useEffect, useState } from "@wordpress/element";
import { create, edit, trash } from "@wordpress/icons";


const REST_NONCE = window.udAccordionBlockSettings?.nonce || "";

function Edit({
	attributes,
	setAttributes,
	textColor,
	backgroundColor,
	setTextColor,
	setBackgroundColor,
	clientId,
}) {
	const {
		layout,
		name,
		role,
		email,
		phone,
		mobile,
		location,
		imageUrl,
		imageId,
		showImage,
	} = attributes;


const { tags = "[]" } = attributes;

const tagArray = (() => {
	try {
		return JSON.parse(tags);
	} catch {
		return [];
	}
})();

const [globalTags, setGlobalTags] = useState([]);

useEffect(() => {
	fetch("/wp-json/ud-shared/v1/tags", {
		headers: {
			"Content-Type": "application/json",
			"X-WP-Nonce": REST_NONCE,
		},
	})
		.then((res) => res.json())
		.then((tags) => {
			if (Array.isArray(tags)) {
				setGlobalTags(tags);
			}
		})
		.catch((err) => console.warn("Fehler beim Laden der Tags:", err));
}, []);


	useEffect(() => {
		if (!imageUrl && window.udPersonalCardPlaceholder) {
			setAttributes({ imageUrl: window.udPersonalCardPlaceholder });
		}
	}, []);

	const isPlaceholder = imageUrl === window.udPersonalCardPlaceholder;

	const onSelectImage = (media) => {
		setAttributes({
			imageUrl: media.url,
			imageId: media.id,
		});
	};

	const removeImage = () => {
		setAttributes({
			imageUrl: window.udPersonalCardPlaceholder,
			imageId: null,
		});
	};

	const blockProps = useBlockProps({
		className: `ud-personal-card ud-layout-${layout} ${textColor?.class} ${backgroundColor?.class}`,
	"data-tags": attributes.tags || "[]", // 👈 explizit im DOM ablegen

	});

	return (
		<>
			<InspectorControls>
				<PanelColorSettings
					title={__('Farben', 'ud')}
					initialOpen={true}
					colorSettings={[
						{
							value: textColor?.color,
							onChange: setTextColor,
							label: __('Textfarbe', 'ud'),
						},
						{
							value: backgroundColor?.color,
							onChange: setBackgroundColor,
							label: __('Hintergrundfarbe', 'ud'),
						},
					]}
				/>

				<PanelBody title={__("Darstellung", "ud")}>
					<ToggleControl
						label={__("Gestapelte Darstellung", "ud")}
						checked={layout === "large"}
						__nextHasNoMarginBottom={true}
						onChange={(value) =>
							setAttributes({ layout: value ? "large" : "small" })
						}
					/>
					<ToggleControl
						label={__("Bild anzeigen", "ud")}
						checked={attributes.showImage}
						__nextHasNoMarginBottom={true}
						onChange={(value) => setAttributes({ showImage: value })}
					/>
				</PanelBody>


<PanelBody title="Tags" initialOpen={true}>
		<FormTokenField
			label="Tags hinzufügen"
			value={tagArray}
			suggestions={globalTags}
			onFocus={() => {
				fetch("/wp-json/ud-shared/v1/tags", {
					headers: {
						"Content-Type": "application/json",
						"X-WP-Nonce": REST_NONCE,
					},
				})
					.then((res) => res.json())
					.then((tags) => {
						if (Array.isArray(tags)) {
							setGlobalTags(tags);
						}
					})
					.catch((err) =>
						console.warn("❌ Fehler beim Nachladen der Tags:", err)
					);
			}}
			onChange={(newTags) => {
				setAttributes({ tags: JSON.stringify(newTags) });

				newTags.forEach((tag) => {
					if (!globalTags.includes(tag)) {
						fetch("/wp-json/ud-shared/v1/tags", {
							method: "POST",
							headers: {
								"Content-Type": "application/json",
								"X-WP-Nonce": REST_NONCE,
							},
							body: JSON.stringify({ name: tag }),
						})
							.then((res) => res.json())
							.then((tags) => {
								if (Array.isArray(tags)) {
									setGlobalTags(tags);
								}
							})
							.catch((err) =>
								console.warn("Tag konnte nicht gespeichert werden", err)
							);
					}
				});
			}}
			__next40pxDefaultSize={true}
			__nextHasNoMarginBottom={true}
		/>
	</PanelBody>

			</InspectorControls>

			<div {...blockProps}>
				{showImage && imageUrl && (
					<div className="ud-personal-image-left">
						<div className="ud-personal-image-wrapper">
							<img
								src={imageUrl}
								alt={__("Profilbild", "ud")}
								className="ud-personal-image"
							/>

							<div className="ud-image-actions">
								<MediaUploadCheck>
									<MediaUpload
										onSelect={onSelectImage}
										allowedTypes={["image"]}
										render={({ open }) => (
											<Button
												onClick={open}
												icon={isPlaceholder ? create : edit}
												label={
													isPlaceholder
														? __("Bild hinzufügen", "ud")
														: __("Bild ändern", "ud")
												}
												size="small"
											/>
										)}
									/>
								</MediaUploadCheck>

								{!isPlaceholder && (
									<Button
										onClick={removeImage}
										variant="secondary"
										icon={trash}
										label={__("Entfernen", "ud")}
										isDestructive
										size="small"
									/>
								)}
							</div>
						</div>
					</div>
				)}

				<div className="ud-personal-content">
					<div className="ud-personal-meta">
						<RichText
							tagName="div"
							className="ud-personal-name"
							value={name}
							onChange={(value) => setAttributes({ name: value })}
							placeholder={__("Name und Vorname eingeben", "ud")}
						/>
						<RichText
							tagName="div"
							className="ud-personal-role"
							value={role}
							onChange={(value) => setAttributes({ role: value })}
							placeholder={__("Funktion eingeben", "ud")}
						/>
					</div>

					<div className="ud-personal-contact">
						<RichText
							tagName="div"
							className="ud-personal-location"
							value={location}
							onChange={(value) => setAttributes({ location: value })}
							placeholder={__("Ort eingeben", "ud")}
						/>
						<RichText
							tagName="div"
							className="ud-personal-email"
							value={email}
							onChange={(value) => setAttributes({ email: value })}
							placeholder={__("E-Mail eingeben", "ud")}
						/>
						<RichText
							tagName="div"
							className="ud-personal-mobile"
							value={mobile}
							onChange={(value) => setAttributes({ mobile: value })}
							placeholder={__("Mobilnummer eingeben", "ud")}
						/>
						<RichText
							tagName="div"
							className="ud-personal-phone"
							value={phone}
							onChange={(value) => setAttributes({ phone: value })}
							placeholder={__("Telefonnummer eingeben", "ud")}
						/>
					</div>
				</div>
			</div>
		</>
	);
}

export default withColors("textColor", "backgroundColor")(Edit);
