<template>
    <div class="triple_grid">
        <div :class="(selected.includes(i) ? 'active' : '')" v-for="(n, i) in 36" :key="i" :data-triple-id="i" @click="tileClick(i)">
        </div>
    </div>
</template>

<script>
    import Bus from '../../../bus';

    export default {
        data() {
            return {
                selected: [],
                autoPickInProcess: false,
				orange: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAASwAAAEsCAMAAABOo35HAAAByFBMVEUAAAD/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////rFH/027ehgDOfQD/qzj+8Kn/szzNewD/qjTchAD/sjn+863/0Wb/rlXMdwDdfwD/sDX/0ZP/rDn/py3//fT/rE//1nH/rVPhigbTgQXonzb+9LD5pjH7xlv/qDD/3pH//vvluHPlr17/+e7+9ej/0mvop0T/uET/3Yr/9OX/7qX/24P/vk/3ojz6pkT/yWH/rjn/xVz/553qkhj+qkz/4JX/4Y3/1HT/slnxmy/9qEn/tT3/5ZT/13rqlyrkjQ3/7KL/6p3/zWf/wlb/u0n/55j//fj//PDznzX++fL/2X7/r0HkkSD/9Nr/w2fXhQvulyTbiBLQfwL/7dD/s0n/z237zWz5qj3hkRbThxT+8+H/2KH/9+v/u2HejBj/6MX/37H/z4v/y4L/wW/+47v/xnX/+On/8sH/8rX/6rT/3av/u1b/t1HvyY/wuWzmrFbfpEj498nvAAAAJnRSTlMA+3fwaUg+OPPm7mB96jNlQ+jjbU1yLijfT9yowwnSuIhSmFgXHyRA8yUAABDoSURBVHja7JxbTxNBFIBLkHpHvGLEK8bNJN3NZLYPa7fxpcnSpG5QIKmYKA/QpIhgpKkvlheggYJoRP+ww2bMadnZPd1ojDs73xN9/XK+szMLNKfRaDQajUaj0Wg0f8Cti5emp6amH17J38tp4pi8dH/UEIzcnp7IaaIYnzpnDHL3cU4j494lUAVMTeY0IZ48MKRcu5HTnE7wjhHByJmcZoCzV41obuY0fYxfM2IYuZ7TDLjStoZs8LyBMKJLFIwHrvRsDdugtjV8g9rWkK4uG4PslcUP7bLeW8i+6h0Ygm8zZWOQjM9WaF89dfcMQY282dAlSvYVuOp8NH6zXte2JHcccOX2+rZXsfimrG1FNehR+gU+7i753Ja+VUc0SCmvEHhTJEVdojgzhFwV3OBZCB0SbStqrgoFl1cIfF3yCdF7S+LK465oZ9foZ6ZItC3Zc5C7KrhHxgDf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',
				blue: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAASwAAAEsCAMAAABOo35HAAACN1BMVEUAAAD///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////9t8v+L7f9Z4OpQxf8siso3le1L3uphy/8Ai+Nv8/9KxP9q8v9Zyf+G7f8Ah+Nl8f81k+0yk+0AieMAhOIkhshx+P9n8v/g+fxf4exuz/9eyv+V3/1Ro9RM4exA3Onv/f71/v9m8v8qiMmO7/9n4v1d4+7j/P9jzv9c5OtG3uoHkeVl7frq/f9i4PUtkez4/v8wkM1n6P+B9P9y5vHf+/+l1/h45vBNs+wFjeRUyP+V9v9+6Pzc7/iq+P9nzv/6///N+v9WxPtFsOvx/f908/+Ry/S9+f+j9//b9v/c+//T+v+O9f8qhsi3+f+c9/85nO6I9f/w+v6g0/bH+/9N0PeYz/Vt5fAVneg4nOV68/9S2vlCp+/X/P9Vte6w+P8xlNFIuvZG0ul38/+h8f7P6vpO1fhm5O9S3+puvvAnres/x+d30//D+f6u5v903v9TyPat8feV6/RL2+4dmOdAvt81mtqP2/9o1v+k8f5Pyvys2ve33vYyo+olouoNluV89P/n+v7r+P7k9v7B6/5W3flJsPRM1/GAxPGE2P/S8P5Yz/dR2eo7sNk2o9QymdG48vfI7v7B5fqG6fEuuOig7vU2sfFh5/Jlsttkz+jA6TmMAAAAI3RSTlMA2oXwEeHmuAx0L5Rvar9/NQh5+K4pszrHokhjWtCLURghQ4rmuBAAABLDSURBVHja7JvNTxNBGMa3BT8Q6wdVEVBAxuW0xHQOMzFwg6TRkF56QKVJSUg0NdFbiUJi04Oxlmo8aHohpZDKhYMHoib+dS7xY2k725l93d2+Y/Z3IyEcfnnnmWdeWiMiIiIiIiIiIiIiIiIiIiIiIiJCwK3xG4mhuM+MnJ+YOnfW+J84OzN0LTZAAiJ5+vrk1C3jv2BwauwUCZzTFxL6+7o0HCMhMXDtxmVDY2auJEmY3EwMGpoyc4GETixh6MjVMdIXro8b2pEYIP1ibNrQiukx0kdujhoaMRojfSU5ZGjD+ADpN8OGJtxIkv4z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',
				purple: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAASwAAAEsCAMAAABOo35HAAAB41BMVEUAAAD///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////+ifP93Nv+NX+2BWu13TvXIsP91Mv/38/9yL/+kf//MtP+OYe2IWe3Eqv/Grf+CT+t4T/aAWe1vKf/Ksv9qPvW1l/b9/P+pjvmfdf90S/azkv6AR/j6+P99Vex4Tux9S/X18P+ngv99Pv7OuP+LXeyFVOyLW+/8+v+bb/9qI//q4f98U+3m3P6gevx9VPWARP+NWP96Ov/ay/1/V+3w6v+IUP+8n/7Bpf6SZu/i1f64mf6cd/OYcfLt5v/k2P9vQvWWa/CDVPTf0P+rjPSMZ/Hy7P/Qu/+wk/XYxv/Tv/9yR/V0SeysiP7Tw/qTbvTf0/yZdfmXcviFWfHczP+UY/+ETfeJZO+HYe+EXu7Vw/+ESf+4nfakgvOPavKxjv+Yaf+RXv/c0PzOu/nHsvmDW/fWxvvAqPe9pPeifPOmifSdfvOJZPjKuPnCrfmog/aJXfGJY/Gig/qPa/moXmH6AAAALXRSTlMAvwj46pS4A0IQ4Igqn/Mc0Of1XRfYOSAkqO9lUDJrDPqBd3FKw3yPyLKtVrsv2YurAAAX10lEQVR42uzWTUsCURQG4Jt9aKVklAqVGqEE6YFhBqEmHPyKiShDbSFWIKKlgoiUJELudRW1Uv9sbUdxd68w0/v8hJdz3nMYAAAAAAAAAAAAAAAAAAAAAAAAAPwHdptjeyO8YjrR9dCmnS1XMBIms/Kc7CwzLpsjSma2FdhfVlx2p4vMzh2ysWUIRrbIArZ3mHi+dbKGsFf4KjpXyCp2Q4LT8pv3CM5b2xNaXF43WcnxocDZ8nvIWlaPmCi+BTuYifGWzcY4u1nwcXmZGEEXzYsVy5PpMM1Vd6jU+1d8paeT1wrNc/uYEAGaU/npNqQ4b7fN2iDFXev7cVrK0CyXkNpyrtGM7NfoIV5QJc7UZu0jOejpGk+yrLdSrXG1RLNE1JbtjGaUO2pclbhTX6o5JXc57smcadpf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'
            }
        },
        methods: {
            tileClick(n) {
                if(this.selected.length >= 3 && !this.selected.includes(n)) return;

                this.playSound('/sounds/click.mp3');

                if(!this.selected.includes(n)) this.selected.push(n);
                else this.selected.splice(this.selected.indexOf(n), 1);
            },
            callback(response) {
				$('[data-triple-id].selected > img').remove();
				$('[data-triple-id].selected').removeClass('selected');
                this.chain(4, 200, (i) => {
					const id = i - 1;
					const user_tiles = response.game.data.user_tiles[id];
					const tripleres = response.game.data.tiles[user_tiles];
					
					$(`[data-triple-id="${user_tiles}"]`).addClass('selected');
					if(tripleres == 0) {
						$(`[data-triple-id="${user_tiles}"]`).append($(`<img style="height: 40px;" class="triple-icon" src="${this.orange}" alt="">`).hide().fadeIn(300));
						this.playSound(`/sounds/ball2.mp3`);
					}
					if(tripleres == 1) {
						$(`[data-triple-id="${user_tiles}"]`).append($(`<img style="height: 40px;" class="triple-icon" src="${this.blue}" alt="">`).hide().fadeIn(300));
						this.playSound(`/sounds/ball2.mp3`);
					}
					if(tripleres == 2) {
						$(`[data-triple-id="${user_tiles}"]`).append($(`<img style="height: 40px;" class="triple-icon" src="${this.purple}" alt="">`).hide().fadeIn(30));
						this.playSound(`/sounds/ball2.mp3`);
					}
					if(i === 4) {
						$(`[data-m="${response.game.multiplier.toFixed(2)}"]`).addClass('highlight');
						this.setGrid(response);
						const win = response.game.win;
						this.playSound(`/sounds/${win ? 'guessed' : 'lose'}.mp3`);
						this.updateGameInstance((i) => i.playTimeout = false);
                        this.resultPopup(response.game);
					}
                });
            },
            errorCallback() {
                this.$toast.error(this.$i18n.t('general.error.empty'));
            },
            gameDataRetrieved() {
				Bus.$emit('triple:history:addEntry', { html: `<div class="history-triple-bar" data-m="14.00">14.00x<div><div class="block-icons"><img class="block-icon" src="${this.purple}" alt=""><img class="block-icon" src="${this.purple}" alt=""><img class="block-icon" src="${this.purple}" alt=""></div>`, type: 'append' });
				Bus.$emit('triple:history:addEntry', { html: `<div class="history-triple-bar" data-m="7.50">7.50x<div><div class="block-icons"><img class="block-icon" src="${this.blue}" alt=""><img class="block-icon" src="${this.blue}" alt=""><img class="block-icon" src="${this.blue}" alt=""></div>`, type: 'append' });
				Bus.$emit('triple:history:addEntry', { html: `<div class="history-triple-bar" data-m="3.50">3.50x<div><div class="block-icons"><img class="block-icon" src="${this.orange}" alt=""><img class="block-icon" src="${this.orange}" alt=""><img class="block-icon" src="${this.orange}" alt=""></div>`, type: 'append' });
                Bus.$emit('triple:history:addEntry', { html: `<div class="history-triple-bar" data-m="2.00">2.00x<div><div class="block-icons"><img class="block-icon" src="${this.purple}" alt=""><img class="block-icon" src="${this.purple}" alt=""><img class="block-icon" src="${this.blue}" alt=""></div>`, type: 'append' });
                Bus.$emit('triple:history:addEntry', { html: `<div class="history-triple-bar" data-m="1.50">1.50x<div><div class="block-icons"><img class="block-icon" src="${this.orange}" alt=""><img class="block-icon" src="${this.orange}" alt=""><img class="block-icon" src="${this.blue}" alt=""></div>`, type: 'append' });
                Bus.$emit('triple:history:addEntry', { html: `<div class="history-triple-bar" data-m="0.60">0.60x<div><div class="block-icons"><img class="block-icon" src="${this.purple}" alt=""><img class="block-icon" src="${this.orange}" alt=""><img class="block-icon" src="${this.blue}" alt=""></div>`, type: 'append' });
                Bus.$emit('triple:history:addEntry', { html: `<div class="history-triple-bar" data-m="0.40">0.40x<div><div class="block-icons"><img class="block-icon" src="${this.purple}" alt=""><img class="block-icon" src="${this.blue}" alt=""><img class="block-icon" src="${this.blue}" alt=""></div>`, type: 'append' });
                Bus.$emit('triple:history:addEntry', { html: `<div class="history-triple-bar" data-m="0.25">0.25x<div><div class="block-icons"><img class="block-icon" src="${this.orange}" alt=""><img class="block-icon" src="${this.blue}" alt=""><img class="block-icon" src="${this.blue}" alt=""></div>`, type: 'append' });
                Bus.$emit('triple:history:addEntry', { html: `<div class="history-triple-bar" data-m="0.10">0.10x<div><div class="block-icons"><img class="block-icon" src="${this.purple}" alt=""><img class="block-icon" src="${this.purple}" alt=""><img class="block-icon" src="${this.orange}" alt=""></div>`, type: 'append' });
            },
            getClientData() {
                return { tiles: this.selected };
            },
            preStart() {
                this.response = [];
            },
			setGrid(response) {
				for(let s = 0; s < 36; s++) {
					const user_tiles = response.game.data.user_tiles[s];
					const tripleres = response.game.data.tiles[s];
					if (s != user_tiles) {
						$(`[data-triple-id="${s}"]`).addClass('selected');
						if(tripleres == 0) {
							$(`[data-triple-id="${s}"]`).append($(`<img style="height: 40px;" class="triple-icon" src="${this.orange}" alt="">`).hide().fadeIn(450));
						}
						if(tripleres == 1) {
							$(`[data-triple-id="${s}"]`).append($(`<img style="height: 40px;" class="triple-icon" src="${this.blue}" alt="">`).hide().fadeIn(450));
						}
						if(tripleres == 2) {
							$(`[data-triple-id="${s}"]`).append($(`<img style="height: 40px;" class="triple-icon" src="${this.purple}" alt="">`).hide().fadeIn(450));
						}
					}
				}
			},
            getSidebarComponents() {
                return [
                    { name: 'label', data: { label: this.$i18n.t('general.wager') } },
                    { name: 'wager-classic' },
                    { name: 'buttons', data: { buttons: [
                        { label: this.$i18n.t('general.autopick'), callback: () => {
                            if(this.autoPickInProcess) return;
                            this.autoPickInProcess = true;

                            this.selected = [];

                            let picked = [];
                            while(picked.length <= 3) {
                                let rand = this.random(1, 36);
                                if(picked.includes(rand)) continue;
                                picked.push(rand);
                            }

                            this.chain(3, 100, (index) => {
                                this.selected.push(picked[index]);
                                this.playSound('/sounds/click.mp3');

                                if(index === 3) this.autoPickInProcess = false;
                            });
                        } },
                        { label: this.$i18n.t('general.clear'), preventMark: true, callback: () => {
                            if(this.autoPickInProcess) return;
							$('[data-triple-id].selected > img').remove();
							$('[data-triple-id].selected').removeClass('selected');
                            this.selected = [];
                        } } ] } },
                    { name: 'auto-bets' },
                    { name: 'play' },
                    { name: 'footer', data: { buttons: ['help', 'sound', 'stats'] } },
                    { name: 'history', data: { scrollable: true } }
                ];
            }
        }
    }
</script>

<style lang="scss">
    @import 'resources/sass/variables';

    .game-history {
        display: flex;
        height: 84px;
    }
	
	.block-icons {
    display: flex;
    align-items: center;
    margin: 2px 0 5px;
	}

	img.block-icon {
		display: block;
		margin-right: 3px;
		width: 13px;
	}

    .history-triple-bar {
		cursor: default;
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
		padding: 5px 10px;
		border-radius: 3px;
		border: 1px solid transparent !important;

		@include themed() {
			color: rgba(t('text'), 0.4) !important;
		}

		&:last-child {
			margin-right: 15px !important;
		}
	}
	
	.history-triple div.highlight {
		@include themed() {
			color: t('text') !important;
			border-color: t('text') !important;
			opacity: 1 !important;
		}
	}

    .game-content-triple {
		display: flex;
		align-items: center;
		justify-content: center;
	}

	.triple_grid {
		display: grid;
		grid-gap: 5px;
		grid-template-columns: repeat(6, 68px);
		grid-template-rows: repeat(6, 68px);
		grid-auto-flow: row;
		position: relative;
		width: 100%;
		justify-content: center;
		transform: translateY(-42px);

		@include themed() {
		
			img.triple-icon.overview {
				height: -webkit-fill-available !important;
			}
			
			div {
				background: rgba(t('text'), 0.1);
				text-align: center;
				transition: background-color 0.15s ease-out, color 0.15s ease-out, top 0.15s ease-out, border-color 0.15s ease-out;
				position: relative;
				border-bottom: 4px solid rgba(t('text'), 0.05);
				border-top: 4px solid transparent;
				border-radius: 2px 2px 3px 3px;

				svg {
					position: absolute;
					width: 100%;
					height: 100%;
					left: 50%;
					top: 50%;
					transform: translate(-50%, -50%);
					opacity: 0;
					transition: opacity 0.15s ease-out;
				}

				&:hover {
					cursor: pointer;
					background: rgba(t('text'), 0.25);
					border-bottom-color: rgba(t('text'), 0.15);
					top: -2px;
				}

				span {
					position: absolute;
					left: 50%;
					top: 50%;
					transform: translate(-50%, -50%);
					font-size: 1.2em;
					font-weight: 600;
				}
			}

			div.active {
				background: t('secondary') !important;
				border-bottom-color: darken(t('secondary'), 5%) !important;
				color: white !important;
			}

			div.selected {
				background: rgba(t('text'), 0.05) !important;
				border-bottom-color: transparent !important;
				border-top-color: rgba(t('text'), 0.1) !important;
				color: #e74c3c !important;
			}

			div.active.selected {
				background: t('secondary') !important;
				border-bottom-color: darken(t('secondary'), 5%) !important;
				color: $gray-700 !important;

				svg {
					opacity: 1;
				}

				span {
					z-index: 10;
					margin-top: -1px;
				}
			}
				img.triple-icon {
					z-index: 2;
					position: absolute;
					top: 50%;
					left: 50%;
					-webkit-transform: translate(-50%,-50%);
					transform: translate(-50%,-50%);
				}
		}

	}

    .overview-render-target .triple_grid {
		grid-template-columns: repeat(6, 35px);
		grid-template-rows: repeat(6, 35px);
		grid-gap: 7px;
		transform: unset !important;
		margin-bottom: 20px;
		margin-top: 10px;

		span {
			font-size: 11px;
		}
	}

    @media (max-width: 1370px){
		.triple_grid {
			grid-template-columns: repeat(6, 5vw);
			grid-template-rows: repeat(6, 5vw);
			grid-gap: 5px;
			font-size: 13px;
		}
    }

    @include media-breakpoint-down(md) {
		.triple_grid {
			grid-template-columns: repeat(6, 10vw);
			grid-template-rows: repeat(6, 10vw);
			grid-gap: 1.5vw;

			font-size: 11px;
		}
		
		img.triple-icon {
			z-index: 2;
			position: absolute;
			top: 50%;
			left: 50%;
			-webkit-transform: translate(-50%,-50%);
			transform: translate(-50%,-50%);
			height: -webkit-fill-available !important;  /* Mozilla игнор. */
			height: -moz-available !important;
			height: stretch !important;
		}
		
		.game-content-triple {
			margin-bottom: 90px;
		}
	}
</style>
