<?php
// RoForgeIDE PHP Library - Full Core Skeleton
// PHP DSL for Roblox scripting -> Lua code

class LuaBuilder {
    private static array $lines = [];
    private static int $varCounter = 0;

    public static function addLine(string $line) {
        self::$lines[] = $line;
    }

    public static function getLua(): string {
        return implode("\n", self::$lines);
    }

    public static function nextVar(string $prefix = "var"): string {
        self::$varCounter++;
        return "{$prefix}" . self::$varCounter;
    }
}

// -------------------------
// Core Roblox Object System
// -------------------------

class Instance {
    public static function new(string $className): RobloxObject {
        $varName = LuaBuilder::nextVar(strtolower($className));
        $obj = new RobloxObject($varName, $className);
        LuaBuilder::addLine("local {$varName} = Instance.new(\"{$className}\")");
        return $obj;
    }
}

class RobloxObject {
    protected string $varName;
    protected string $className;

    public function __construct(string $varName, string $className) {
        $this->varName = $varName;
        $this->className = $className;
    }

    // -------------------
    // Common Properties
    // -------------------

    public function destroy(string $object): self {
        LuaBuilder::addLine("{$this->varName}::Destroy()");
        return $this;
    }
    
    public function setParent(string $parent): self {
        LuaBuilder::addLine("{$this->varName}.Parent = game.{$parent}");
        return $this;
    }

    public function setBrickColour(string $color): self {
        LuaBuilder::addLine("{$this->varName}.BrickColor = BrickColor.new(\"{$color}\")");
        return $this;
    }

    public function setAnchored(bool $anchored): self {
        $value = $anchored ? "true" : "false";
        LuaBuilder::addLine("{$this->varName}.Anchored = {$value}");
        return $this;
    }

    public function setPosition(float $x, float $y, float $z): self {
        LuaBuilder::addLine("{$this->varName}.Position = Vector3.new({$x}, {$y}, {$z})");
        return $this;
    }

    public function setSize(float $x, float $y, float $z = 0): self {
        if ($z === 0) {
            LuaBuilder::addLine("{$this->varName}.Size = UDim2.new({$x}, {$y})");
        } else {
            LuaBuilder::addLine("{$this->varName}.Size = Vector3.new({$x}, {$y}, {$z})");
        }
        return $this;
    }

    public function setText(string $text): self {
        LuaBuilder::addLine("{$this->varName}.Text = \"{$text}\"");
        return $this;
    }

    // Generic property setter
    public function set(string $prop, $value): self {
        $val = is_bool($value) ? ($value ? "true" : "false") : (is_string($value) ? "\"$value\"" : $value);
        LuaBuilder::addLine("{$this->varName}.{$prop} = {$val}");
        return $this;
    }

    // Events
    public function onTouched(string $callbackVarName): self {
        LuaBuilder::addLine("{$this->varName}.Touched:Connect({$callbackVarName})");
        return $this;
    }

    public function onChanged(string $property, string $callbackVarName): self {
        LuaBuilder::addLine("{$this->varName}.Changed:Connect(function(prop) if prop == \"{$property}\" then {$callbackVarName}() end end)");
        return $this;
    }
}

// -------------------------
// Workspace & Players
// -------------------------
class Workspace {
    public static function add(RobloxObject $obj): RobloxObject {
        return $obj->setParent("Workspace");
    }
}

class Players {
    public static function get(string $playerName): RobloxObject {
        $varName = LuaBuilder::nextVar("player");
        LuaBuilder::addLine("local {$varName} = game.Players:FindFirstChild(\"{$playerName}\")");
        return new RobloxObject($varName, "Player");
    }
}

// -------------------------
// GUI Classes
// -------------------------
class ScreenGui extends RobloxObject {
    public function __construct() {
        $varName = LuaBuilder::nextVar("screenGui");
        parent::__construct($varName, "ScreenGui");
        LuaBuilder::addLine("local {$varName} = Instance.new(\"ScreenGui\")");
    }
}

class Frame extends RobloxObject {
    public function __construct() {
        $varName = LuaBuilder::nextVar("frame");
        parent::__construct($varName, "Frame");
        LuaBuilder::addLine("local {$varName} = Instance.new(\"Frame\")");
    }
}

class TextLabel extends RobloxObject {
    public function __construct() {
        $varName = LuaBuilder::nextVar("textLabel");
        parent::__construct($varName, "TextLabel");
        LuaBuilder::addLine("local {$varName} = Instance.new(\"TextLabel\")");
    }
}

// -------------------------
// Vector3 / CFrame Helpers
// -------------------------
class Vector3 {
    public static function new(float $x, float $y, float $z): string {
        return "Vector3.new({$x}, {$y}, {$z})";
    }
}

class CFrame {
    public static function new(float $x, float $y, float $z): string {
        return "CFrame.new({$x}, {$y}, {$z})";
    }
}

// -------------------------
// Example Usage
// -------------------------
/*
$part = Instance::new("Part");
$part->setParent("Workspace")
     ->setBrickColour("Really Red")
     ->setAnchored(true)
     ->setPosition(0,10,0);

$player = Players::get("skyss_0fly");

$gui = new ScreenGui();
$frame = new Frame();
$label = new TextLabel();
$label->setText("Hello World")->setSize(0.3, 0.1);

echo LuaBuilder::getLua();
*/
