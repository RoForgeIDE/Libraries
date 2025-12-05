<?php
// RoForgeIDE PHP Library - Lua Transpiler Backend

class LuaBuilder {
    private static array $lines = [];

    public static function addLine(string $line) {
        self::$lines[] = $line;
    }

    public static function getLua(): string {
        return implode("\n", self::$lines);
    }
}

// Roblox Objects
class Instance {
    public static function new(string $className): RobloxObject {
        $luaVar = 'part'; // for simplicity, you can add auto var naming later
        $obj = new RobloxObject($luaVar, $className);
        LuaBuilder::addLine("local {$luaVar} = Instance.new(\"{$className}\")");
        return $obj;
    }
}

class RobloxObject {
    private string $varName;
    private string $className;

    public function __construct(string $varName, string $className) {
        $this->varName = $varName;
        $this->className = $className;
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

    // Add more Roblox properties/methods here
}

// Example Usage:
/*
$part = Instance::new("Part");
$part->setParent("Workspace")
     ->setBrickColour("Really Red")
     ->setAnchored(true);

// Export Lua
echo LuaBuilder::getLua();

*/
