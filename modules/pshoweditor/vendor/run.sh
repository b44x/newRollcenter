#!/bin/bash

# Generator struktury alias klas PSM
# Tworzy pliki w odpowiedniej strukturze katalogów

OUTPUT_DIR="alias_classes"
mkdir -p "$OUTPUT_DIR"

# Funkcja do tworzenia pliku z klasą
create_class_file() {
    local namespace=$1
    local classname=$2
    local extends=$3
    local implements=$4
    local is_abstract=$5
    
    # Konwertuj namespace na ścieżkę katalogów
    local path=$(echo "$namespace" | tr '\\' '/')
    local dir="$OUTPUT_DIR/$path"
    mkdir -p "$dir"
    
    # Utwórz plik
    local file="$dir/$classname.php"
    
    local abstract_keyword=""
    [ "$is_abstract" = "true" ] && abstract_keyword="abstract "
    
    local implements_str=""
    [ -n "$implements" ] && implements_str=" implements $implements"
    
    cat > "$file" << 'EOF'
<?php
EOF
    
    [ -n "$namespace" ] && echo "namespace $namespace;" >> "$file"
    echo "" >> "$file"
    echo "${abstract_keyword}class $classname extends $extends$implements_str {}" >> "$file"
    
    echo "✓ Utworzono: $file"
}

# Prestashow\PrestaBlockEditor
create_class_file "Prestashow\\PrestaBlockEditor" "BlockEditor" "\\PShowEditorScoped\\Prestashow\\PrestaBlockEditor\\BlockEditor" "" "false"

# Prestashow\PrestaBlockEditor\Service
create_class_file "Prestashow\\PrestaBlockEditor\\Service" "ImageService" "\\PShowEditorScoped\\Prestashow\\PrestaBlockEditor\\Service\\ImageService" "" "false"

# Prestashow\PrestaCore\Adapter
create_class_file "Prestashow\\PrestaCore\\Adapter" "UpdateService" "\\PShowEditorScoped\\Prestashow\\PrestaCore\\Adapter\\UpdateService" "" "false"
create_class_file "Prestashow\\PrestaCore\\Adapter" "ServiceAdapter" "\\PShowEditorScoped\\Prestashow\\PrestaCore\\Adapter\\ServiceAdapter" "" "false"
create_class_file "Prestashow\\PrestaCore\\Adapter" "UpdateServiceAdapter" "\\PShowEditorScoped\\Prestashow\\PrestaCore\\Adapter\\UpdateServiceAdapter" "" "false"

# Prestashow\PrestaCore\Composer
create_class_file "Prestashow\\PrestaCore\\Composer" "ScopingPlugin" "\\PShowEditorScoped\\Prestashow\\PrestaCore\\Composer\\ScopingPlugin" "\\Composer\\Plugin\\PluginInterface, \\Composer\\EventDispatcher\\EventSubscriberInterface" "false"

# Prestashow\PrestaCore\Controller
create_class_file "Prestashow\\PrestaCore\\Controller" "BackupController" "\\PShowEditorScoped\\Prestashow\\PrestaCore\\Controller\\BackupController" "" "false"
create_class_file "Prestashow\\PrestaCore\\Controller" "HookController" "\\PShowEditorScoped\\Prestashow\\PrestaCore\\Controller\\HookController" "" "false"
create_class_file "Prestashow\\PrestaCore\\Controller" "SettingsController" "\\PShowEditorScoped\\Prestashow\\PrestaCore\\Controller\\SettingsController" "" "false"
create_class_file "Prestashow\\PrestaCore\\Controller" "UpdateController" "\\PShowEditorScoped\\Prestashow\\PrestaCore\\Controller\\UpdateController" "" "false"

# Prestashow\PrestaCore\Database\Migrations
create_class_file "Prestashow\\PrestaCore\\Database\\Migrations" "AbstractMigration" "\\PShowEditorScoped\\Prestashow\\PrestaCore\\Database\\Migrations\\AbstractMigration" "" "true"
create_class_file "Prestashow\\PrestaCore\\Database\\Migrations" "MigrationCoreTool" "\\PShowEditorScoped\\Prestashow\\PrestaCore\\Database\\Migrations\\MigrationCoreTool" "" "false"
create_class_file "Prestashow\\PrestaCore\\Database\\Migrations" "MigrationTool" "\\PShowEditorScoped\\Prestashow\\PrestaCore\\Database\\Migrations\\MigrationTool" "" "false"
create_class_file "Prestashow\\PrestaCore\\Database\\Migrations" "Version0" "\\PShowEditorScoped\\Prestashow\\PrestaCore\\Database\\Migrations\\Version0" "" "false"
create_class_file "Prestashow\\PrestaCore\\Database\\Migrations" "Version1" "\\PShowEditorScoped\\Prestashow\\PrestaCore\\Database\\Migrations\\Version1" "" "false"

# Prestashow\PrestaCore\Entity
create_class_file "Prestashow\\PrestaCore\\Entity" "Hook" "\\PShowEditorScoped\\Prestashow\\PrestaCore\\Entity\\Hook" "" "false"
create_class_file "Prestashow\\PrestaCore\\Entity" "Notification" "\\PShowEditorScoped\\Prestashow\\PrestaCore\\Entity\\Notification" "" "false"
create_class_file "Prestashow\\PrestaCore\\Entity" "NotificationRead" "\\PShowEditorScoped\\Prestashow\\PrestaCore\\Entity\\NotificationRead" "" "false"

# Prestashow\PrestaCore\Exception
create_class_file "Prestashow\\PrestaCore\\Exception" "PrestashowException" "\\PShowEditorScoped\\Prestashow\\PrestaCore\\Exception\\PrestashowException" "" "false"
create_class_file "Prestashow\\PrestaCore\\Exception" "UpdateException" "\\PShowEditorScoped\\Prestashow\\PrestaCore\\Exception\\UpdateException" "" "false"

# Prestashow\PrestaCore\Model
create_class_file "Prestashow\\PrestaCore\\Model" "AbstractAdminController" "\\PShowEditorScoped\\Prestashow\\PrestaCore\\Model\\AbstractAdminController" "" "true"
create_class_file "Prestashow\\PrestaCore\\Model" "AbstractDemoContent" "\\PShowEditorScoped\\Prestashow\\PrestaCore\\Model\\AbstractDemoContent" "" "true"
create_class_file "Prestashow\\PrestaCore\\Model" "AbstractEntity" "\\PShowEditorScoped\\Prestashow\\PrestaCore\\Model\\AbstractEntity" "" "true"
create_class_file "Prestashow\\PrestaCore\\Model" "AbstractModule" "\\PShowEditorScoped\\Prestashow\\PrestaCore\\Model\\AbstractModule" "" "true"
create_class_file "Prestashow\\PrestaCore\\Model" "AbstractRepository" "\\PShowEditorScoped\\Prestashow\\PrestaCore\\Model\\AbstractRepository" "" "false"
create_class_file "Prestashow\\PrestaCore\\Model" "AbstractService" "\\PShowEditorScoped\\Prestashow\\PrestaCore\\Model\\AbstractService" "" "false"
create_class_file "Prestashow\\PrestaCore\\Model" "DemoObjectModel" "\\PShowEditorScoped\\Prestashow\\PrestaCore\\Model\\DemoObjectModel" "" "false"
create_class_file "Prestashow\\PrestaCore\\Model" "ModuleSettings" "\\PShowEditorScoped\\Prestashow\\PrestaCore\\Model\\ModuleSettings" "" "false"

# Prestashow\PrestaCore\Service
create_class_file "Prestashow\\PrestaCore\\Service" "DatabaseService" "\\PShowEditorScoped\\Prestashow\\PrestaCore\\Service\\DatabaseService" "" "false"
create_class_file "Prestashow\\PrestaCore\\Service" "DemoContentService" "\\PShowEditorScoped\\Prestashow\\PrestaCore\\Service\\DemoContentService" "" "false"
create_class_file "Prestashow\\PrestaCore\\Service" "IniService" "\\PShowEditorScoped\\Prestashow\\PrestaCore\\Service\\IniService" "" "false"
create_class_file "Prestashow\\PrestaCore\\Service" "OverrideService" "\\PShowEditorScoped\\Prestashow\\PrestaCore\\Service\\OverrideService" "" "false"
create_class_file "Prestashow\\PrestaCore\\Service" "RecommendationService" "\\PShowEditorScoped\\Prestashow\\PrestaCore\\Service\\RecommendationService" "" "false"
create_class_file "Prestashow\\PrestaCore\\Service" "ToolsService" "\\PShowEditorScoped\\Prestashow\\PrestaCore\\Service\\ToolsService" "" "false"
create_class_file "Prestashow\\PrestaCore\\Service" "TranslationService" "\\PShowEditorScoped\\Prestashow\\PrestaCore\\Service\\TranslationService" "" "false"

# Prestashow\PrestaCore\Util
create_class_file "Prestashow\\PrestaCore\\Util" "HookOverrideFix" "\\PShowEditorScoped\\Prestashow\\PrestaCore\\Util\\HookOverrideFix" "" "false"

# Prestashow\PrestaUpdate\Model
create_class_file "Prestashow\\PrestaUpdate\\Model" "License" "\\PShowEditorScoped\\Prestashow\\PrestaUpdate\\Model\\License" "" "false"

# Prestashow\PrestaUpdate\Service
create_class_file "Prestashow\\PrestaUpdate\\Service" "MultistoreService" "\\PShowEditorScoped\\Prestashow\\PrestaUpdate\\Service\\MultistoreService" "" "false"
create_class_file "Prestashow\\PrestaUpdate\\Service" "UpdateService" "\\PShowEditorScoped\\Prestashow\\PrestaUpdate\\Service\\UpdateService" "" "false"

# Global namespace class
cat > "$OUTPUT_DIR/PrestashowAutoload.php" << 'EOF'
<?php

class PrestashowAutoload extends \PShowEditorScoped_PrestashowAutoload {}
EOF
echo "✓ Utworzono: $OUTPUT_DIR/PrestashowAutoload.php"

echo ""
echo "✅ Struktura alias klas utworzona w katalogu: $OUTPUT_DIR"
