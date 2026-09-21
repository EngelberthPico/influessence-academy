<?php

namespace App\Providers\Filament;

use Filament\Actions\Action;
use Filament\FontProviders\LocalFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->brandName('Influessence Academy')
            ->brandLogo(asset('images/brand/logo-espresso.png'))
            ->brandLogoHeight('2rem')
            ->homeUrl(fn (): string => route('home'))
            ->darkMode(false)
            ->font('Poppins', provider: LocalFontProvider::class)
            ->login()
            ->colors([
                // Color::hex('#946B4A') sube demasiado el chroma en los tonos medios
                // (se ve naranja, no marrón apagado). Esta paleta a mano conserva el
                // matiz y el chroma reales de la marca (oklch(0.562 0.07 59.509)) y
                // solo varía la luminosidad entre los 11 pasos.
                'primary' => [
                    50 => '#fff0cb',
                    100 => '#ffeac6',
                    200 => '#ffdab6',
                    300 => '#f8caa7',
                    400 => '#c19674',
                    500 => '#946b4a',
                    600 => '#754f2f',
                    700 => '#5a3615',
                    800 => '#3f1d00',
                    900 => '#2e0d00',
                    950 => '#1c0000',
                ],
                // Mismos 11 tonos que --color-zinc-* en resources/css/app.css, para que
                // el panel y el área privada ("Mi cuenta") compartan la misma escala.
                'gray' => [
                    50 => '#fffee8',
                    100 => '#f9f7e1',
                    200 => '#eae6cf',
                    300 => '#dad5bd',
                    400 => '#a79b83',
                    500 => '#7b6852',
                    600 => '#634f3a',
                    700 => '#553f2c',
                    800 => '#412b19',
                    900 => '#362314',
                    950 => '#2a1b10',
                ],
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn () => view('filament.partials.fonts'),
            )
            ->renderHook(
                PanelsRenderHook::USER_MENU_BEFORE,
                fn () => view('filament.partials.topbar-account-button'),
            )
            ->userMenuItems([
                'account' => Action::make('account')
                    ->label('Mi cuenta')
                    ->icon('heroicon-o-user')
                    ->url(fn (): string => route('learning.index')),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
