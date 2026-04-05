<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $slug = '23810310088-products';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Thông tin cơ bản')->schema([
                Grid::make(2)->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn($state, $set) =>
                            $set('slug', Str::slug($state))
                        ),
                    TextInput::make('slug')
                        ->required()
                        ->unique(ignoreRecord: true),
                ]),
                Grid::make(2)->schema([
                    Select::make('category_id')
                        ->label('Danh mục')
                        ->relationship('category', 'name')
                        ->required(),
                    Select::make('status')
                        ->options([
                            'draft'        => 'Nháp',
                            'published'    => 'Công khai',
                            'out_of_stock' => 'Hết hàng',
                        ])
                        ->required(),
                ]),
                Grid::make(3)->schema([
                    TextInput::make('price')
                        ->numeric()
                        ->required()
                        ->minValue(0)
                        ->label('Giá (VNĐ)')
                        ->suffix('đ'),
                    TextInput::make('stock_quantity')
                        ->numeric()
                        ->required()
                        ->integer()
                        ->minValue(0)
                        ->label('Tồn kho'),
                    TextInput::make('discount_percent')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->suffix('%')
                        ->label('Giảm giá (%)'),
                ]),
            ]),
            Section::make('Mô tả & Hình ảnh')->schema([
                RichEditor::make('description')
                    ->label('Mô tả sản phẩm')
                    ->columnSpanFull()
                    ->toolbarButtons([
                        'bold', 'italic', 'underline',
                        'bulletList', 'orderedList', 'link',
                    ]),
                FileUpload::make('image_path')
                    ->label('Ảnh đại diện')
                    ->image()
                    ->directory('products')
                    ->maxSize(2048),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_path')->label('Ảnh')->circular(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('category.name')->label('Danh mục')->badge(),
                TextColumn::make('price')
                    ->label('Giá')
                    ->formatStateUsing(fn($state) =>
                        number_format($state, 0, ',', '.') . ' đ'
                    )
                    ->sortable(),
                TextColumn::make('discount_percent')
                    ->label('Giảm')
                    ->suffix('%')
                    ->color(fn($state) => $state > 0 ? 'danger' : 'gray'),
                TextColumn::make('stock_quantity')->label('Tồn kho')->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'published'    => 'success',
                        'draft'        => 'warning',
                        'out_of_stock' => 'danger',
                    }),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Danh mục')
                    ->relationship('category', 'name'),
                SelectFilter::make('status')
                    ->options([
                        'draft'        => 'Nháp',
                        'published'    => 'Công khai',
                        'out_of_stock' => 'Hết hàng',
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit'   => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}