<x-yarn-quick-create-modal
    name="quick-add-project"
    openKey="quick-add-project"
    title="Projekt anlegen"
    description="Wenn das passende Projekt noch fehlt, kannst du es direkt hier anlegen und danach sofort im Garnformular weiterarbeiten."
    :action="route('projects.store')"
    errorBag="quickAddProject"
    nameField="quick_project_name"
    namePlaceholder="z. B. Winterpullover"
    notesField="quick_project_notes"
    notesPlaceholder="Optional: Muster, Größe oder Idee"
    submitLabel="Projekt speichern"
/>

<x-yarn-quick-create-modal
    name="quick-add-color"
    openKey="quick-add-color"
    title="Farbe anlegen"
    description="Neue Farbe speichern und direkt im aktuellen Garn auswählen."
    :action="route('colors.store')"
    errorBag="quickAddColor"
    nameField="quick_color_name"
    namePlaceholder="z. B. Senfgelb"
    submitLabel="Farbe speichern"
/>

<x-yarn-quick-create-modal
    name="quick-add-material"
    openKey="quick-add-material"
    title="Material anlegen"
    description="Lege ein neues Material an, ohne das Garnformular zu verlassen."
    :action="route('materials.store')"
    errorBag="quickAddMaterial"
    nameField="quick_material_name"
    namePlaceholder="z. B. Merino"
    submitLabel="Material speichern"
/>

<x-yarn-quick-create-modal
    name="quick-add-brand"
    openKey="quick-add-brand"
    title="Marke anlegen"
    description="Falls der Hersteller noch fehlt, kannst du ihn hier direkt ergänzen."
    :action="route('brands.store')"
    errorBag="quickAddBrand"
    nameField="quick_brand_name"
    namePlaceholder="z. B. Sandnes Garn"
    submitLabel="Marke speichern"
/>

<x-yarn-quick-create-modal
    name="quick-add-location"
    openKey="quick-add-location"
    title="Ort anlegen"
    description="Neue Lagerorte wie Box, Regal oder Schrank direkt ergänzen."
    :action="route('locations.store')"
    errorBag="quickAddLocation"
    nameField="quick_location_name"
    namePlaceholder="z. B. Wohnzimmer-Regal"
    submitLabel="Ort speichern"
/>
