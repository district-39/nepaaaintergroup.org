<?php

class APVC_Admin_Html
{
    public static function get_header()
    {
        ?>
        <header class="apvc_header">
            <div class="apvc_logo">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="170pt" height="45pt"
                     viewBox="0 0 540 112.499997" version="1.2">
                    <defs>
                        <g>
                            <symbol overflow="visible" id="glyph0-0">
                                <path style="stroke:none;"
                                      d="M 0 0 L 0 -25.453125 L 18.171875 -25.453125 L 18.171875 0 Z M 9.09375 -14.359375 L 15.265625 -23.640625 L 2.90625 -23.640625 Z M 10.1875 -12.71875 L 16.359375 -3.453125 L 16.359375 -22 Z M 2.90625 -1.8125 L 15.265625 -1.8125 L 9.09375 -11.09375 Z M 1.8125 -22 L 1.8125 -3.453125 L 8 -12.71875 Z M 1.8125 -22 "/>
                            </symbol>
                            <symbol overflow="visible" id="glyph0-1">
                                <path style="stroke:none;"
                                      d="M 19.453125 -4.6875 L 8.328125 -4.6875 L 6.46875 0 L -0.1875 0 L 10.796875 -25.453125 L 17.453125 -25.453125 L 28.171875 0 L 21.265625 0 Z M 17.5625 -9.59375 L 13.921875 -19.015625 L 10.21875 -9.59375 Z M 17.5625 -9.59375 "/>
                            </symbol>
                            <symbol overflow="visible" id="glyph0-2">
                                <path style="stroke:none;"
                                      d="M 22.25 -26.984375 L 22.25 0 L 15.96875 0 L 15.96875 -2.578125 C 14.53125 -0.691406 12.566406 0.25 10.078125 0.25 C 7.304688 0.25 5.082031 -0.679688 3.40625 -2.546875 C 1.738281 -4.410156 0.90625 -6.859375 0.90625 -9.890625 C 0.90625 -12.890625 1.726562 -15.300781 3.375 -17.125 C 5.03125 -18.945312 7.210938 -19.859375 9.921875 -19.859375 C 12.515625 -19.859375 14.53125 -18.910156 15.96875 -17.015625 L 15.96875 -26.984375 Z M 8.4375 -6.03125 C 9.238281 -5.09375 10.289062 -4.625 11.59375 -4.625 C 12.90625 -4.625 13.960938 -5.09375 14.765625 -6.03125 C 15.566406 -6.976562 15.96875 -8.21875 15.96875 -9.75 C 15.96875 -11.25 15.566406 -12.46875 14.765625 -13.40625 C 13.960938 -14.351562 12.90625 -14.828125 11.59375 -14.828125 C 10.289062 -14.828125 9.238281 -14.359375 8.4375 -13.421875 C 7.632812 -12.492188 7.222656 -11.269531 7.203125 -9.75 C 7.222656 -8.21875 7.632812 -6.976562 8.4375 -6.03125 Z M 8.4375 -6.03125 "/>
                            </symbol>
                            <symbol overflow="visible" id="glyph0-3">
                                <path style="stroke:none;"
                                      d="M 13.640625 0 L 7.203125 0 L -0.1875 -19.59375 L 6.296875 -19.59375 L 10.515625 -5.75 L 14.765625 -19.59375 L 21.015625 -19.59375 Z M 13.640625 0 "/>
                            </symbol>
                            <symbol overflow="visible" id="glyph0-4">
                                <path style="stroke:none;"
                                      d="M 11.3125 -19.890625 C 14 -19.890625 16.085938 -19.265625 17.578125 -18.015625 C 19.066406 -16.765625 19.828125 -15.023438 19.859375 -12.796875 L 19.859375 0 L 13.703125 0 L 13.703125 -2.25 C 12.421875 -0.550781 10.421875 0.296875 7.703125 0.296875 C 5.648438 0.296875 4.015625 -0.269531 2.796875 -1.40625 C 1.585938 -2.550781 0.984375 -4.035156 0.984375 -5.859375 C 0.984375 -7.722656 1.628906 -9.171875 2.921875 -10.203125 C 4.222656 -11.234375 6.085938 -11.757812 8.515625 -11.78125 L 13.671875 -11.78125 L 13.671875 -12.078125 C 13.671875 -13.046875 13.347656 -13.800781 12.703125 -14.34375 C 12.066406 -14.882812 11.117188 -15.15625 9.859375 -15.15625 C 8.085938 -15.15625 6.132812 -14.613281 4 -13.53125 L 2.21875 -17.671875 C 5.394531 -19.148438 8.425781 -19.890625 11.3125 -19.890625 Z M 9.640625 -3.96875 C 10.628906 -3.96875 11.5 -4.210938 12.25 -4.703125 C 13 -5.203125 13.472656 -5.851562 13.671875 -6.65625 L 13.671875 -8.40625 L 9.703125 -8.40625 C 7.816406 -8.40625 6.875 -7.703125 6.875 -6.296875 C 6.875 -5.566406 7.113281 -4.992188 7.59375 -4.578125 C 8.082031 -4.171875 8.765625 -3.96875 9.640625 -3.96875 Z M 9.640625 -3.96875 "/>
                            </symbol>
                            <symbol overflow="visible" id="glyph0-5">
                                <path style="stroke:none;"
                                      d="M 15.34375 -19.890625 C 17.5 -19.890625 19.226562 -19.203125 20.53125 -17.828125 C 21.84375 -16.460938 22.5 -14.640625 22.5 -12.359375 L 22.5 0 L 16.25 0 L 16.25 -10.609375 C 16.25 -11.753906 15.9375 -12.660156 15.3125 -13.328125 C 14.695312 -13.992188 13.859375 -14.328125 12.796875 -14.328125 C 11.679688 -14.328125 10.765625 -13.960938 10.046875 -13.234375 C 9.335938 -12.503906 8.921875 -11.546875 8.796875 -10.359375 L 8.796875 0 L 2.515625 0 L 2.515625 -19.59375 L 8.796875 -19.59375 L 8.796875 -16.390625 C 10.179688 -18.671875 12.363281 -19.835938 15.34375 -19.890625 Z M 15.34375 -19.890625 "/>
                            </symbol>
                            <symbol overflow="visible" id="glyph0-6">
                                <path style="stroke:none;"
                                      d="M 11.046875 -19.8125 C 12.992188 -19.8125 14.722656 -19.429688 16.234375 -18.671875 C 17.742188 -17.910156 18.925781 -16.828125 19.78125 -15.421875 L 15.265625 -12.6875 C 14.296875 -14.039062 12.976562 -14.71875 11.3125 -14.71875 C 10.09375 -14.71875 9.101562 -14.265625 8.34375 -13.359375 C 7.582031 -12.453125 7.203125 -11.257812 7.203125 -9.78125 C 7.203125 -8.28125 7.578125 -7.070312 8.328125 -6.15625 C 9.078125 -5.25 10.070312 -4.796875 11.3125 -4.796875 C 13.15625 -4.796875 14.488281 -5.535156 15.3125 -7.015625 L 19.890625 -4.328125 C 19.085938 -2.867188 17.914062 -1.742188 16.375 -0.953125 C 14.84375 -0.171875 13.046875 0.21875 10.984375 0.21875 C 7.953125 0.21875 5.507812 -0.691406 3.65625 -2.515625 C 1.800781 -4.347656 0.875 -6.757812 0.875 -9.75 C 0.875 -12.75 1.8125 -15.175781 3.6875 -17.03125 C 5.570312 -18.882812 8.023438 -19.8125 11.046875 -19.8125 Z M 11.046875 -19.8125 "/>
                            </symbol>
                            <symbol overflow="visible" id="glyph0-7">
                                <path style="stroke:none;"
                                      d="M 11.09375 -19.8125 C 14.488281 -19.8125 17.039062 -18.769531 18.75 -16.6875 C 20.457031 -14.601562 21.203125 -11.742188 20.984375 -8.109375 L 7.203125 -8.109375 C 7.515625 -6.941406 8.070312 -6.039062 8.875 -5.40625 C 9.675781 -4.78125 10.65625 -4.46875 11.8125 -4.46875 C 13.601562 -4.46875 15.164062 -5.132812 16.5 -6.46875 L 19.8125 -3.234375 C 17.707031 -0.929688 14.859375 0.21875 11.265625 0.21875 C 8.097656 0.21875 5.570312 -0.6875 3.6875 -2.5 C 1.8125 -4.320312 0.875 -6.738281 0.875 -9.75 C 0.875 -12.78125 1.8125 -15.210938 3.6875 -17.046875 C 5.570312 -18.890625 8.039062 -19.8125 11.09375 -19.8125 Z M 7.09375 -11.484375 L 14.9375 -11.484375 C 14.9375 -12.671875 14.59375 -13.628906 13.90625 -14.359375 C 13.21875 -15.085938 12.300781 -15.453125 11.15625 -15.453125 C 10.09375 -15.453125 9.195312 -15.09375 8.46875 -14.375 C 7.738281 -13.664062 7.28125 -12.703125 7.09375 -11.484375 Z M 7.09375 -11.484375 "/>
                            </symbol>
                            <symbol overflow="visible" id="glyph0-8">
                                <path style="stroke:none;" d=""/>
                            </symbol>
                            <symbol overflow="visible" id="glyph0-9">
                                <path style="stroke:none;"
                                      d="M 2.546875 -25.453125 L 13.640625 -25.453125 C 16.878906 -25.453125 19.394531 -24.679688 21.1875 -23.140625 C 22.988281 -21.597656 23.890625 -19.410156 23.890625 -16.578125 C 23.890625 -13.597656 22.988281 -11.28125 21.1875 -9.625 C 19.394531 -7.976562 16.878906 -7.15625 13.640625 -7.15625 L 9.015625 -7.15625 L 9.015625 0 L 2.546875 0 Z M 9.015625 -20.359375 L 9.015625 -12.25 L 13.3125 -12.25 C 14.738281 -12.25 15.835938 -12.597656 16.609375 -13.296875 C 17.390625 -14.003906 17.78125 -15.023438 17.78125 -16.359375 C 17.78125 -17.671875 17.390625 -18.664062 16.609375 -19.34375 C 15.835938 -20.019531 14.738281 -20.359375 13.3125 -20.359375 Z M 9.015625 -20.359375 "/>
                            </symbol>
                            <symbol overflow="visible" id="glyph0-10">
                                <path style="stroke:none;"
                                      d="M 21.234375 -19.59375 L 21.234375 -1.78125 C 21.234375 0.976562 20.253906 3.179688 18.296875 4.828125 C 16.347656 6.484375 13.785156 7.3125 10.609375 7.3125 C 7.367188 7.3125 4.460938 6.453125 1.890625 4.734375 L 4.078125 0.46875 C 5.960938 1.78125 7.984375 2.4375 10.140625 2.4375 C 11.648438 2.4375 12.832031 2.070312 13.6875 1.34375 C 14.550781 0.613281 14.984375 -0.363281 14.984375 -1.59375 L 14.984375 -3.671875 C 13.648438 -1.921875 11.769531 -1.046875 9.34375 -1.046875 C 6.84375 -1.046875 4.8125 -1.921875 3.25 -3.671875 C 1.6875 -5.421875 0.90625 -7.710938 0.90625 -10.546875 C 0.90625 -13.304688 1.664062 -15.539062 3.1875 -17.25 C 4.71875 -18.957031 6.722656 -19.8125 9.203125 -19.8125 C 11.691406 -19.84375 13.617188 -18.972656 14.984375 -17.203125 L 14.984375 -19.59375 Z M 10.90625 -5.59375 C 12.113281 -5.59375 13.09375 -6.035156 13.84375 -6.921875 C 14.601562 -7.804688 14.984375 -8.953125 14.984375 -10.359375 C 14.984375 -11.785156 14.601562 -12.941406 13.84375 -13.828125 C 13.09375 -14.710938 12.113281 -15.15625 10.90625 -15.15625 C 9.664062 -15.15625 8.664062 -14.710938 7.90625 -13.828125 C 7.144531 -12.941406 6.765625 -11.785156 6.765625 -10.359375 C 6.765625 -8.953125 7.144531 -7.804688 7.90625 -6.921875 C 8.664062 -6.035156 9.664062 -5.59375 10.90625 -5.59375 Z M 10.90625 -5.59375 "/>
                            </symbol>
                            <symbol overflow="visible" id="glyph0-11">
                                <path style="stroke:none;"
                                      d="M 16.546875 0 L 9.859375 0 L -0.03125 -25.453125 L 6.875 -25.453125 L 13.375 -6.71875 L 19.890625 -25.453125 L 26.546875 -25.453125 Z M 16.546875 0 "/>
                            </symbol>
                            <symbol overflow="visible" id="glyph0-12">
                                <path style="stroke:none;"
                                      d="M 3.28125 -27.609375 C 3.90625 -28.234375 4.703125 -28.546875 5.671875 -28.546875 C 6.640625 -28.546875 7.429688 -28.234375 8.046875 -27.609375 C 8.671875 -26.992188 8.984375 -26.203125 8.984375 -25.234375 C 8.984375 -24.265625 8.671875 -23.460938 8.046875 -22.828125 C 7.429688 -22.203125 6.640625 -21.890625 5.671875 -21.890625 C 4.703125 -21.890625 3.90625 -22.203125 3.28125 -22.828125 C 2.664062 -23.460938 2.359375 -24.265625 2.359375 -25.234375 C 2.359375 -26.203125 2.664062 -26.992188 3.28125 -27.609375 Z M 8.765625 0 L 2.515625 0 L 2.515625 -19.59375 L 8.765625 -19.59375 Z M 8.765625 0 "/>
                            </symbol>
                            <symbol overflow="visible" id="glyph0-13">
                                <path style="stroke:none;"
                                      d="M 9.53125 -19.890625 C 12.414062 -19.890625 15.101562 -19.148438 17.59375 -17.671875 L 15.453125 -13.59375 C 13.023438 -14.90625 10.960938 -15.5625 9.265625 -15.5625 C 7.984375 -15.5625 7.34375 -15.113281 7.34375 -14.21875 C 7.34375 -13.726562 7.691406 -13.316406 8.390625 -12.984375 C 9.097656 -12.660156 9.957031 -12.363281 10.96875 -12.09375 C 11.988281 -11.832031 13.003906 -11.503906 14.015625 -11.109375 C 15.023438 -10.710938 15.878906 -10.078125 16.578125 -9.203125 C 17.285156 -8.328125 17.640625 -7.257812 17.640625 -6 C 17.640625 -4.03125 16.875 -2.492188 15.34375 -1.390625 C 13.8125 -0.296875 11.820312 0.25 9.375 0.25 C 5.914062 0.25 2.984375 -0.671875 0.578125 -2.515625 L 2.578125 -6.515625 C 4.878906 -4.890625 7.195312 -4.078125 9.53125 -4.078125 C 10.957031 -4.078125 11.671875 -4.523438 11.671875 -5.421875 C 11.671875 -5.929688 11.320312 -6.351562 10.625 -6.6875 C 9.9375 -7.03125 9.097656 -7.332031 8.109375 -7.59375 C 7.117188 -7.863281 6.125 -8.195312 5.125 -8.59375 C 4.132812 -9 3.289062 -9.625 2.59375 -10.46875 C 1.90625 -11.320312 1.5625 -12.390625 1.5625 -13.671875 C 1.5625 -15.628906 2.300781 -17.15625 3.78125 -18.25 C 5.257812 -19.34375 7.175781 -19.890625 9.53125 -19.890625 Z M 9.53125 -19.890625 "/>
                            </symbol>
                            <symbol overflow="visible" id="glyph0-14">
                                <path style="stroke:none;"
                                      d="M 13.921875 -5.421875 L 15.15625 -1.046875 C 13.632812 -0.148438 11.890625 0.296875 9.921875 0.296875 C 7.984375 0.296875 6.414062 -0.238281 5.21875 -1.3125 C 4.019531 -2.394531 3.421875 -3.929688 3.421875 -5.921875 L 3.421875 -14.578125 L 0.6875 -14.578125 L 0.6875 -18.6875 L 3.421875 -18.6875 L 3.421875 -24.140625 L 9.671875 -24.140625 L 9.671875 -18.71875 L 14.796875 -18.71875 L 14.796875 -14.578125 L 9.671875 -14.578125 L 9.671875 -6.90625 C 9.671875 -6.15625 9.820312 -5.601562 10.125 -5.25 C 10.425781 -4.90625 10.878906 -4.742188 11.484375 -4.765625 C 12.066406 -4.765625 12.878906 -4.984375 13.921875 -5.421875 Z M 13.921875 -5.421875 "/>
                            </symbol>
                            <symbol overflow="visible" id="glyph0-15">
                                <path style="stroke:none;"
                                      d="M 14.46875 -25.671875 C 16.363281 -25.671875 18.222656 -25.28125 20.046875 -24.5 C 21.878906 -23.726562 23.4375 -22.6875 24.71875 -21.375 L 20.984375 -16.9375 C 20.160156 -17.957031 19.171875 -18.765625 18.015625 -19.359375 C 16.859375 -19.953125 15.710938 -20.25 14.578125 -20.25 C 12.566406 -20.25 10.878906 -19.539062 9.515625 -18.125 C 8.160156 -16.707031 7.484375 -14.957031 7.484375 -12.875 C 7.484375 -10.738281 8.160156 -8.953125 9.515625 -7.515625 C 10.878906 -6.085938 12.566406 -5.375 14.578125 -5.375 C 15.648438 -5.375 16.757812 -5.640625 17.90625 -6.171875 C 19.0625 -6.710938 20.085938 -7.441406 20.984375 -8.359375 L 24.765625 -4.359375 C 23.378906 -2.953125 21.742188 -1.820312 19.859375 -0.96875 C 17.984375 -0.125 16.113281 0.296875 14.25 0.296875 C 10.425781 0.296875 7.238281 -0.953125 4.6875 -3.453125 C 2.144531 -5.953125 0.875 -9.066406 0.875 -12.796875 C 0.875 -16.453125 2.171875 -19.507812 4.765625 -21.96875 C 7.359375 -24.4375 10.59375 -25.671875 14.46875 -25.671875 Z M 14.46875 -25.671875 "/>
                            </symbol>
                            <symbol overflow="visible" id="glyph0-16">
                                <path style="stroke:none;"
                                      d="M 11.59375 -19.8125 C 14.789062 -19.8125 17.367188 -18.894531 19.328125 -17.0625 C 21.296875 -15.238281 22.28125 -12.820312 22.28125 -9.8125 C 22.28125 -6.8125 21.296875 -4.390625 19.328125 -2.546875 C 17.367188 -0.703125 14.789062 0.21875 11.59375 0.21875 C 8.375 0.21875 5.78125 -0.703125 3.8125 -2.546875 C 1.851562 -4.390625 0.875 -6.8125 0.875 -9.8125 C 0.875 -12.820312 1.851562 -15.238281 3.8125 -17.0625 C 5.78125 -18.894531 8.375 -19.8125 11.59375 -19.8125 Z M 11.59375 -14.828125 C 10.289062 -14.828125 9.234375 -14.351562 8.421875 -13.40625 C 7.609375 -12.46875 7.203125 -11.25 7.203125 -9.75 C 7.203125 -8.21875 7.609375 -6.984375 8.421875 -6.046875 C 9.234375 -5.117188 10.289062 -4.65625 11.59375 -4.65625 C 12.882812 -4.65625 13.9375 -5.117188 14.75 -6.046875 C 15.5625 -6.984375 15.96875 -8.21875 15.96875 -9.75 C 15.96875 -11.25 15.5625 -12.46875 14.75 -13.40625 C 13.9375 -14.351562 12.882812 -14.828125 11.59375 -14.828125 Z M 11.59375 -14.828125 "/>
                            </symbol>
                            <symbol overflow="visible" id="glyph0-17">
                                <path style="stroke:none;"
                                      d="M 21.59375 -19.59375 L 21.59375 0 L 15.3125 0 L 15.3125 -3.234375 C 13.976562 -0.910156 11.851562 0.25 8.9375 0.25 C 6.8125 0.25 5.109375 -0.425781 3.828125 -1.78125 C 2.554688 -3.132812 1.921875 -4.953125 1.921875 -7.234375 L 1.921875 -19.59375 L 8.21875 -19.59375 L 8.21875 -9.015625 C 8.21875 -7.847656 8.515625 -6.929688 9.109375 -6.265625 C 9.703125 -5.597656 10.492188 -5.265625 11.484375 -5.265625 C 12.671875 -5.296875 13.601562 -5.738281 14.28125 -6.59375 C 14.96875 -7.457031 15.3125 -8.566406 15.3125 -9.921875 L 15.3125 -19.59375 Z M 21.59375 -19.59375 "/>
                            </symbol>
                            <symbol overflow="visible" id="glyph0-18">
                                <path style="stroke:none;"
                                      d="M 8.796875 -19.59375 L 8.796875 -16.28125 C 10.179688 -18.632812 12.300781 -19.835938 15.15625 -19.890625 L 15.15625 -14.21875 C 13.363281 -14.382812 11.894531 -14.085938 10.75 -13.328125 C 9.613281 -12.566406 8.960938 -11.484375 8.796875 -10.078125 L 8.796875 0 L 2.515625 0 L 2.515625 -19.59375 Z M 8.796875 -19.59375 "/>
                            </symbol>
                            <symbol overflow="visible" id="glyph1-0">
                                <path style="stroke:none;"
                                      d="M 0 0 L 0 -92.453125 L 66.046875 -92.453125 L 66.046875 0 Z M 33.015625 -52.171875 L 55.46875 -85.84375 L 10.5625 -85.84375 Z M 36.984375 -46.234375 L 59.4375 -12.546875 L 59.4375 -79.90625 Z M 10.5625 -6.609375 L 55.46875 -6.609375 L 33.015625 -40.28125 Z M 6.609375 -79.90625 L 6.609375 -12.546875 L 29.0625 -46.234375 Z M 6.609375 -79.90625 "/>
                            </symbol>
                            <symbol overflow="visible" id="glyph1-1">
                                <path style="stroke:none;"
                                      d="M 41.46875 0 L 17.96875 0 L 17.96875 -73.703125 L 0.921875 -73.703125 L 0.921875 -92.59375 L 41.46875 -92.59375 Z M 41.46875 0 "/>
                            </symbol>
                        </g>
                        <filter id="alpha" filterUnits="objectBoundingBox" x="0%" y="0%" width="100%" height="100%">
                            <feColorMatrix type="matrix" in="SourceGraphic" values="0 0 0 0 1 0 0 0 0 1 0 0 0 0 1 0 0 0 1 0"/>
                        </filter>
                        <mask id="mask0">
                            <g filter="url(#alpha)">
                                <rect x="0" y="0" width="540" height="112.499997"
                                      style="fill:rgb(0%,0%,0%);fill-opacity:0.8588;stroke:none;"/>
                            </g>
                        </mask>
                        <clipPath id="clip2">
                            <path d="M 37.09375 0.601562 L 57.179688 0.601562 L 57.179688 94.988281 L 37.09375 94.988281 Z M 37.09375 0.601562 "/>
                        </clipPath>
                        <clipPath id="clip1">
                            <rect x="0" y="0" width="95" height="96"/>
                        </clipPath>
                        <g id="surface5" clip-path="url(#clip1)">
                            <g clip-path="url(#clip2)" clip-rule="nonzero">
                                <path style=" stroke:none;fill-rule:nonzero;fill:rgb(100%,8.628845%,8.628845%);fill-opacity:1;"
                                      d="M 94.488281 94.988281 L 0.101562 94.988281 L 0.101562 0.601562 L 94.488281 0.601562 Z M 94.488281 94.988281 "/>
                            </g>
                        </g>
                        <clipPath id="clip3">
                            <path d="M 12.441406 25.015625 L 106.828125 25.015625 L 106.828125 45.097656 L 12.441406 45.097656 Z M 12.441406 25.015625 "/>
                        </clipPath>
                        <clipPath id="clip4">
                            <path d="M 1.195312 7 L 88 7 L 88 103 L 1.195312 103 Z M 1.195312 7 "/>
                        </clipPath>
                        <mask id="mask1">
                            <g filter="url(#alpha)">
                                <rect x="0" y="0" width="540" height="112.499997"
                                      style="fill:rgb(0%,0%,0%);fill-opacity:0.7569;stroke:none;"/>
                            </g>
                        </mask>
                        <clipPath id="clip6">
                            <path d="M 28.613281 0.601562 L 48.695312 0.601562 L 48.695312 94.988281 L 28.613281 94.988281 Z M 28.613281 0.601562 "/>
                        </clipPath>
                        <clipPath id="clip5">
                            <rect x="0" y="0" width="87" height="96"/>
                        </clipPath>
                        <g id="surface8" clip-path="url(#clip5)">
                            <g clip-path="url(#clip6)" clip-rule="nonzero">
                                <path style=" stroke:none;fill-rule:nonzero;fill:rgb(100%,8.628845%,8.628845%);fill-opacity:1;"
                                      d="M 86.003906 94.988281 L -8.382812 94.988281 L -8.382812 0.601562 L 86.003906 0.601562 Z M 86.003906 94.988281 "/>
                            </g>
                        </g>
                        <clipPath id="clip7">
                            <path d="M 12 26 L 108 26 L 108 112.003906 L 12 112.003906 Z M 12 26 "/>
                        </clipPath>
                        <mask id="mask2">
                            <g filter="url(#alpha)">
                                <rect x="0" y="0" width="540" height="112.499997"
                                      style="fill:rgb(0%,0%,0%);fill-opacity:0.7373;stroke:none;"/>
                            </g>
                        </mask>
                        <clipPath id="clip9">
                            <path d="M 0.441406 37.496094 L 94.828125 37.496094 L 94.828125 57.578125 L 0.441406 57.578125 Z M 0.441406 37.496094 "/>
                        </clipPath>
                        <clipPath id="clip8">
                            <rect x="0" y="0" width="96" height="87"/>
                        </clipPath>
                        <g id="surface11" clip-path="url(#clip8)">
                            <g clip-path="url(#clip9)" clip-rule="nonzero">
                                <path style=" stroke:none;fill-rule:nonzero;fill:rgb(0%,29.019165%,67.83905%);fill-opacity:1;"
                                      d="M 94.828125 0.1875 L 94.828125 94.574219 L 0.441406 94.574219 L 0.441406 0.1875 Z M 94.828125 0.1875 "/>
                            </g>
                        </g>
                    </defs>
                    <g id="surface1">
                        <g style="fill:rgb(0%,29.019165%,67.83905%);fill-opacity:1;">
                            <use xlink:href="#glyph0-1" x="176.786416" y="47.817178"/>
                        </g>
                        <g style="fill:rgb(0%,29.019165%,67.83905%);fill-opacity:1;">
                            <use xlink:href="#glyph0-2" x="204.776773" y="47.817178"/>
                        </g>
                        <g style="fill:rgb(0%,29.019165%,67.83905%);fill-opacity:1;">
                            <use xlink:href="#glyph0-3" x="229.71364" y="47.817178"/>
                        </g>
                        <g style="fill:rgb(0%,29.019165%,67.83905%);fill-opacity:1;">
                            <use xlink:href="#glyph0-4" x="250.470129" y="47.817178"/>
                        </g>
                        <g style="fill:rgb(0%,29.019165%,67.83905%);fill-opacity:1;">
                            <use xlink:href="#glyph0-5" x="272.280806" y="47.817178"/>
                        </g>
                        <g style="fill:rgb(0%,29.019165%,67.83905%);fill-opacity:1;">
                            <use xlink:href="#glyph0-6" x="296.745107" y="47.817178"/>
                        </g>
                        <g style="fill:rgb(0%,29.019165%,67.83905%);fill-opacity:1;">
                            <use xlink:href="#glyph0-7" x="317.174428" y="47.817178"/>
                        </g>
                        <g style="fill:rgb(0%,29.019165%,67.83905%);fill-opacity:1;">
                            <use xlink:href="#glyph0-2" x="339.057804" y="47.817178"/>
                        </g>
                        <g style="fill:rgb(0%,29.019165%,67.83905%);fill-opacity:1;">
                            <use xlink:href="#glyph0-8" x="363.993593" y="47.817178"/>
                        </g>
                        <g style="fill:rgb(0%,29.019165%,67.83905%);fill-opacity:1;">
                            <use xlink:href="#glyph0-9" x="176.786416" y="86.348823"/>
                        </g>
                        <g style="fill:rgb(0%,29.019165%,67.83905%);fill-opacity:1;">
                            <use xlink:href="#glyph0-4" x="201.505171" y="86.348823"/>
                        </g>
                        <g style="fill:rgb(0%,29.019165%,67.83905%);fill-opacity:1;">
                            <use xlink:href="#glyph0-10" x="223.315848" y="86.348823"/>
                        </g>
                        <g style="fill:rgb(0%,29.019165%,67.83905%);fill-opacity:1;">
                            <use xlink:href="#glyph0-7" x="246.835016" y="86.348823"/>
                        </g>
                        <g style="fill:rgb(0%,29.019165%,67.83905%);fill-opacity:1;">
                            <use xlink:href="#glyph0-8" x="268.718392" y="86.348823"/>
                        </g>
                        <g style="fill:rgb(0%,29.019165%,67.83905%);fill-opacity:1;">
                            <use xlink:href="#glyph0-11" x="277.151845" y="86.348823"/>
                        </g>
                        <g style="fill:rgb(0%,29.019165%,67.83905%);fill-opacity:1;">
                            <use xlink:href="#glyph0-12" x="303.651806" y="86.348823"/>
                        </g>
                        <g style="fill:rgb(0%,29.019165%,67.83905%);fill-opacity:1;">
                            <use xlink:href="#glyph0-13" x="314.957012" y="86.348823"/>
                        </g>
                        <g style="fill:rgb(0%,29.019165%,67.83905%);fill-opacity:1;">
                            <use xlink:href="#glyph0-12" x="333.605141" y="86.348823"/>
                        </g>
                        <g style="fill:rgb(0%,29.019165%,67.83905%);fill-opacity:1;">
                            <use xlink:href="#glyph0-14" x="344.910346" y="86.348823"/>
                        </g>
                        <g style="fill:rgb(0%,29.019165%,67.83905%);fill-opacity:1;">
                            <use xlink:href="#glyph0-8" x="360.723081" y="86.348823"/>
                        </g>
                        <g style="fill:rgb(0%,29.019165%,67.83905%);fill-opacity:1;">
                            <use xlink:href="#glyph0-15" x="369.156535" y="86.348823"/>
                        </g>
                        <g style="fill:rgb(0%,29.019165%,67.83905%);fill-opacity:1;">
                            <use xlink:href="#glyph0-16" x="394.311513" y="86.348823"/>
                        </g>
                        <g style="fill:rgb(0%,29.019165%,67.83905%);fill-opacity:1;">
                            <use xlink:href="#glyph0-17" x="417.467172" y="86.348823"/>
                        </g>
                        <g style="fill:rgb(0%,29.019165%,67.83905%);fill-opacity:1;">
                            <use xlink:href="#glyph0-5" x="441.567963" y="86.348823"/>
                        </g>
                        <g style="fill:rgb(0%,29.019165%,67.83905%);fill-opacity:1;">
                            <use xlink:href="#glyph0-14" x="466.032264" y="86.348823"/>
                        </g>
                        <g style="fill:rgb(0%,29.019165%,67.83905%);fill-opacity:1;">
                            <use xlink:href="#glyph0-7" x="481.844999" y="86.348823"/>
                        </g>
                        <g style="fill:rgb(0%,29.019165%,67.83905%);fill-opacity:1;">
                            <use xlink:href="#glyph0-18" x="503.72839" y="86.348823"/>
                        </g>
                        <use xlink:href="#surface5" transform="matrix(1,0,0,1,31,7)" mask="url(#mask0)"/>
                        <g clip-path="url(#clip3)" clip-rule="nonzero">
                            <path style=" stroke:none;fill-rule:nonzero;fill:rgb(0%,29.019165%,67.83905%);fill-opacity:1;"
                                  d="M 106.828125 -12.292969 L 106.828125 82.089844 L 12.441406 82.089844 L 12.441406 -12.292969 Z M 106.828125 -12.292969 "/>
                        </g>
                        <g clip-path="url(#clip4)" clip-rule="nonzero">
                            <use xlink:href="#surface8" transform="matrix(1,0,0,1,1,7)" mask="url(#mask1)"/>
                        </g>
                        <g clip-path="url(#clip7)" clip-rule="nonzero">
                            <use xlink:href="#surface11" transform="matrix(1,0,0,1,12,26)" mask="url(#mask2)"/>
                        </g>
                        <g style="fill:rgb(0%,29.019165%,67.83905%);fill-opacity:1;">
                            <use xlink:href="#glyph1-1" x="117.124475" y="100.437046"/>
                        </g>
                    </g>
                </svg>
            </div>
            <div class="apvc_links">
                <a href="https://wordpress.org/support/plugin/advanced-page-visit-counter/reviews/#new-post" target="_blank"><?php 
        echo  esc_html_e( 'Leave us a Review', 'apvc' ) ;
        ?> <i class='bx bxs-star'></i></a>
                <a href="https://helpdesk.pagevisitcounter.com/" target="_blank"><?php 
        echo  esc_html_e( 'Support', 'apvc' ) ;
        ?> <i class='bx bxs-help-circle' ></i></a>
            </div>

        </header>
        <!-- Menu -->
        <nav class="apvc_menu">
            <ul>
                <li class="<?php 
        if ( !isset( $_GET['section'] ) ) {
            echo  'active' ;
        }
        ?>">
                    <a href="<?php 
        echo  esc_url( APVC_DASHBOARD_PAGE_LINK ) ;
        ?>">
                        <i class="menu-icon tf-icons bx bx-home-circle"></i>
                        <div><?php 
        esc_html_e( 'Dashboards', 'apvc' );
        ?></div>
                    </a>
                </li>

                <li class="<?php 
        echo  APVC_Admin_Helpers::is_active_menu( 'apvc-trending' ) ;
        ?>">
                    <a href="<?php 
        echo  esc_url( APVC_DASHBOARD_PAGE_LINK ) ;
        ?>&section=apvc-trending">
                        <i class="menu-icon tf-icons bx bx-trending-up"></i>
                        <div><?php 
        esc_html_e( 'Trending', 'apvc' );
        ?></div>
                    </a>
                </li>
                <li class="<?php 
        echo  APVC_Admin_Helpers::is_active_menu( 'apvc-reports' ) ;
        ?>">
                    <a href="<?php 
        echo  esc_url( APVC_DASHBOARD_PAGE_LINK ) ;
        ?>&section=apvc-reports">
                        <i class="menu-icon tf-icons bx bxs-report"></i>
                        <div><?php 
        esc_html_e( 'Reports', 'apvc' );
        ?></div>
                    </a>
                </li>
                <?php 
        /* ?>
           <li class="<?php echo APVC_Admin_Helpers::is_active_menu( 'apvc-referrers' ); ?>">
               <a href="<?php echo esc_url( APVC_DASHBOARD_PAGE_LINK ); ?>&section=apvc-referrers">
                   <i class="menu-icon tf-icons bx bx-globe"></i>
                   <div><?php esc_html_e( 'Referrers', 'apvc' ); ?></div>
               </a>
           </li>
           <?php */
        ?>

                <li class="<?php 
        echo  APVC_Admin_Helpers::is_active_menu( 'apvc-shortcodes' ) ;
        ?> <?php 
        echo  APVC_Admin_Helpers::is_active_menu( 'apvc-shortcode-generator' ) ;
        ?>">
                    <a href="<?php 
        echo  esc_url( APVC_DASHBOARD_PAGE_LINK ) ;
        ?>&section=apvc-shortcodes">
                        <i class="menu-icon tf-icons bx bxs-barcode"></i><div><?php 
        esc_html_e( 'Shortcodes', 'apvc' );
        ?></div>
                    </a>
                </li>

                <?php 
        ?>

                <li class="<?php 
        echo  APVC_Admin_Helpers::is_active_menu( 'apvc-settings' ) ;
        ?>">
                    <a href="<?php 
        echo  esc_url( APVC_DASHBOARD_PAGE_LINK ) ;
        ?>&section=apvc-settings">
                        <i class="menu-icon tf-icons bx bxs-cog"></i><div><?php 
        esc_html_e( 'Settings', 'apvc' );
        ?></div>
                    </a>
                </li>

                <?php 
        ?>
                    <li>
                        <a href="https://pagevisitcounter.com/pricing/" target="_blank" style=" color: #fff;">
                            <i class="menu-icon tf-icons bx bxs-cart"></i><div><?php 
        esc_html_e( 'BUY NOW! @50% DISCOUNT', 'apvc' );
        ?></div>
                        </a>
                    </li>
                <?php 
        ?>

            </ul>
        </nav>
        <!-- / Menu -->
        <?php 
    }
    
    public static function get_total_visit_by_year()
    {
        ?>
		<div class="col-md-6 col-md-6 col-lg-3 mb-2">
			<div class="card">
				<div class="card-body text-center tv_yearly">
					<div class="avatar avatar-md mx-auto mb-3">
						<span class="avatar-initial rounded-circle bg-label-info"><i class='bx bx-calendar fs-3'></i></span>
					</div>
					<span class="d-block mb-1 text-nowrap">
                        <?php 
        _e( 'Total Visits', 'apvc' );
        ?><br />
                        <?php 
        _e( '(This 1 Year)', 'apvc' );
        ?>
                    </span>
					<h2 class="mb-0 tv_yearly_val">0 </h2>
                    <div class="diff_period_yearly"></div>
				</div>
			</div>
		</div>
		<?php 
    }
    
    public static function get_total_visit_by_month()
    {
        ?>
		<div class="col-lg-3 col-md-6 col-12 mb-2 tv_monthly">
			<div class="card">
				<div class="card-body text-center">
					<div class="avatar avatar-md mx-auto mb-3">
						<span class="avatar-initial rounded-circle bg-label-info"><i class='bx bx-calendar fs-3'></i></span>
					</div>
					<span class="d-block mb-1 text-nowrap">
                        <?php 
        _e( 'Total Visits', 'apvc' );
        ?>
                        <br />
                        <?php 
        _e( '(This Month)', 'apvc' );
        ?>
                    </span>
					<h2 class="mb-0 tv_monthly_val">0</h2>
                    <div class="diff_period_monthly"></div>
				</div>
			</div>
		</div>
		<?php 
    }
    
    public static function get_total_visit_by_week()
    {
        ?>
		<div class="col-lg-3 col-md-6 col-12 mb-2 tv_weekly">
			<div class="card">
				<div class="card-body text-center">
					<div class="avatar avatar-md mx-auto mb-3">
						<span class="avatar-initial rounded-circle bg-label-info"><i class='bx bx-calendar fs-3'></i></span>
					</div>
					<span class="d-block mb-1 text-nowrap">
                        <?php 
        _e( 'Total Visits', 'apvc' );
        ?>
                        <br />
                        <?php 
        _e( '(This Week)', 'apvc' );
        ?>
					    <h2 class="mb-0 tv_weekly_val">0</h2>
                        <div class="diff_period_weekly"></div>
				</div>
			</div>
		</div>
		<?php 
    }
    
    public static function get_total_visit_by_day()
    {
        ?>
		<div class="col-lg-3 col-md-6 col-12 mb-2 tv_daily">
			<div class="card">
				<div class="card-body text-center">
					<div class="avatar avatar-md mx-auto mb-3">
						<span class="avatar-initial rounded-circle bg-label-info"><i class='bx bx-calendar fs-3'></i></span>
					</div>
					<span class="d-block mb-1 text-nowrap">
                        <?php 
        _e( 'Total Visits', 'apvc' );
        ?>
                        <br />
                        <?php 
        _e( '(Today)', 'apvc' );
        ?>
                    </span>
					<h2 class="mb-0 tv_daily_val">0</h2>
                    <div class="diff_period_daily"></div>
				</div>
			</div>
		</div>
		<?php 
    }
    
    public static function get_total_visitors( $type = 'dashboard' )
    {
        ?>
		<div class="col-lg-3 col-md-6 col-12 mb-2">
			<div class="card">
				<div class="card-body text-center">
					<div class="avatar avatar-md mx-auto mb-3">
						<span class="avatar-initial rounded-circle bg-label-info"><i class='bx bx-user-circle fs-3'></i></span>
					</div>
					<span class="d-block mb-1 text-nowrap"><?php 
        _e( 'Visitors', 'apvc' );
        ?></span>
					<h2 class="mb-0 apvc_dash_visitors_rng"><?php 
        echo  APVC_Query::get_dashboard_visitors_with_range_by_field(
            'visitors',
            '',
            '',
            $type
        ) ;
        ?></h2>
				</div>
			</div>
		</div>
		<?php 
    }
    
    public static function get_total_views( $type = 'dashboard' )
    {
        ?>
		<div class="col-lg-3 col-md-6 col-12 mb-2">
			<div class="card">
				<div class="card-body text-center">
					<div class="avatar avatar-md mx-auto mb-3">
						<span class="avatar-initial rounded-circle bg-label-info"><i class='bx bxs-time fs-3'></i></span>
					</div>
					<span class="d-block mb-1 text-nowrap"><?php 
        _e( 'Views', 'apvc' );
        ?></span>
					<h2 class="mb-0 apvc_dash_views_rng"><?php 
        echo  APVC_Query::get_dashboard_visitors_with_range_by_field(
            'views',
            '',
            '',
            $type
        ) ;
        ?></h2>
				</div>
			</div>
		</div>
		<?php 
    }
    
    public static function get_total_user_sessions( $type = 'dashboard' )
    {
        ?>
		<div class="col-lg-3 col-md-6 col-12 mb-2">
			<div class="card">
				<div class="card-body text-center">
					<div class="avatar avatar-md mx-auto mb-3">
						<span class="avatar-initial rounded-circle bg-label-info"><i class='bx bxs-user fs-3'></i></span>
					</div>
					<span class="d-block mb-1 text-nowrap"><?php 
        _e( 'User sessions', 'apvc' );
        ?></span>
					<h2 class="mb-0 apvc_dash_sessions_rng"><?php 
        echo  APVC_Query::get_dashboard_visitors_with_range_by_field(
            'sessions',
            '',
            '',
            $type
        ) ;
        ?></h2>
				</div>
			</div>
		</div>
		<?php 
    }
    
    public static function get_unique_visitors()
    {
        ?>
		<div class="col-lg-3 col-md-6 col-12 mb-2">
			<div class="card">
				<div class="card-body text-center">
					<div class="avatar avatar-md mx-auto mb-3">
						<span class="avatar-initial rounded-circle bg-label-info"><i class='bx bx-user-plus fs-3' ></i></span>
					</div>
					<span class="d-block mb-1 text-nowrap"><?php 
        _e( 'First time visitors', 'apvc' );
        ?></span>
					<h2 class="mb-0 apvc_dash_unq_vis_rng"><?php 
        echo  APVC_Query::get_dashboard_visitors_with_range_by_field( 'unq_visitors' ) ;
        ?></h2>
				</div>
			</div>
		</div>
		<?php 
    }
    
    public static function get_trending_referrer()
    {
        ?>
		<div class="col-lg-3 col-md-6 col-12 mb-2 tr_trending_referrer">
			<div class="card">
				<div class="card-body text-center">
					<div class="avatar avatar-md mx-auto mb-3">
						<span class="avatar-initial rounded-circle bg-label-info"><i class='bx bx-trending-up fs-3' ></i></span>
					</div>
					<span class="d-block mb-1 text-nowrap"><?php 
        _e( 'Trending Ref', 'apvc' );
        ?></span>
					<h2 class="mb-0 apvc_ref_name tr_trending_referrer_body">-</h2>
				</div>
			</div>
		</div>
		<?php 
    }
    
    public static function get_main_chart()
    {
        ?>
		<div class="col-lg-12 col-md-12 mb-4">
			<div class="card">
				<div class="card-header header-elements">
					<h5 class="card-title mb-0"><?php 
        _e( 'Statistics', 'apvc' );
        ?></h5>
				</div>
				<div class="card-body pt-2">
					<canvas id="apvc_dashboard_chart" class="chartjs" data-height="500" height="500"></canvas>
				</div>
			</div>
		</div>


		<?php 
    }
    
    public static function get_dashboard_list_table()
    {
        $allPosts = APVC_Query::get_dashboard_list_posts( 10 );
        ?>
		<div class="col-lg-12 col-md-12 col-12 mb-4">
			<div class="card">
			<div class="table-responsive text-nowrap">
				<table class="table">
					<thead>
					<tr>
						<th><?php 
        esc_html_e( 'Page ID', 'apvc' );
        ?></th>
						<th><?php 
        esc_html_e( 'Page Title', 'apvc' );
        ?></th>
						<th><?php 
        esc_html_e( 'Page Views', 'apvc' );
        ?></th>
						<th><?php 
        esc_html_e( 'User Sessions', 'apvc' );
        ?></th>
						<th><?php 
        esc_html_e( 'Post Type', 'apvc' );
        ?></th>
					</tr>
					</thead>
					<tbody class="table-border-bottom-0">
					<?php 
        
        if ( !empty($allPosts) ) {
            foreach ( $allPosts as $post ) {
                ?>
						<tr>
							<td><?php 
                echo  $post->singular_id ;
                ?></td>
							<td><?php 
                echo  ( 45 < strlen( $post->stored_title ) ? substr( $post->stored_title, 0, 45 ) . '...' : $post->stored_title ) ;
                ?> <a href="<?php 
                echo  $post->stored_url ;
                ?>" target="_blank"><i class='bx bx-link-external'></i></a></td>
							<td><?php 
                echo  APVC_Admin_Helpers::filter_count_format( $post->views ) ;
                ?></td>
							<td><?php 
                echo  APVC_Admin_Helpers::filter_count_format( APVC_Query::get_post_total_sessions( $post->singular_id ) ) ;
                ?></td>
							<td><?php 
                echo  $post->stored_type_label ;
                ?></td>
						</tr>
							<?php 
            }
        } else {
            ?>
						<tr>
							<td colspan="7" class="text-center pt-5"><h5><?php 
            esc_html_e( 'No posts found!', 'apvc' );
            ?></h5></td>
						</tr>
					<?php 
        }
        
        ?>
					</tbody>
				</table>
				<div class="action-btns text-center mt-5">
					<a class="btn btn-primary text-nowrap" href="<?php 
        echo  esc_url( APVC_DASHBOARD_SETTINGS_PAGE_LINK . '&section=apvc-reports' ) ;
        ?>"><?php 
        esc_html_e( 'View all posts', 'apvc' );
        ?></a>
				</div>
			</div>
		</div>
		</div>
		<?php 
    }
    
    public static function get_admin_dashboard()
    {
        do_action( 'apvc_dashboard_before' );
        self::get_total_visit_by_year();
        self::get_total_visit_by_month();
        self::get_total_visit_by_week();
        self::get_total_visit_by_day();
        
        if ( true === APVC_Admin_Helpers::check_page( 'advanced-page-visit-counter-dashboard' ) ) {
            ?>
			<input type="hidden" value="dashboard" id="apvc_page_id">
			<div id="apvc-overlay"></div>
			<div class="accordion accordion-header-primary" id="apvc_advanced_filters">
				<div class="accordion-item card">
					<h2 class="accordion-header">
						<button type="button" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#apvc-advanced-filters" aria-expanded="false">
							<?php 
            esc_html_e( 'Advanced Filters', 'apvc' );
            ?>&nbsp;&nbsp;<i class="bx bx-calendar"></i>
						</button>
					</h2>
					<div id="apvc-advanced-filters" class="accordion-collapse collapse" data-bs-parent="#apvc-advanced-filters" style="">
						<div class="accordion-body">
							<div class="col-4">
								<label for="bs-rangepicker-range" class="form-label"><?php 
            esc_html_e( 'Date Ranges', 'apvc' );
            ?></label>
								<input type="text" id="bs-rangepicker-range" class="form-control" />
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php 
        }
        
        self::get_total_visitors();
        self::get_total_views();
        self::get_total_user_sessions();
        self::get_unique_visitors();
        self::get_main_chart();
        self::get_dashboard_list_table();
        do_action( 'apvc_dashboard_after' );
    }
    
    public static function get_admin_settings()
    {
        echo  '<div class=" apvc-settings-page">' ;
        echo  '<div class="row">' ;
        do_action( 'apvc_settings_before' );
        self::get_settings_widget_one();
        self::get_settings_widgets_two();
        do_action( 'apvc_settings_after' );
        echo  '</div>' ;
        echo  '</div>' ;
    }
    
    public static function get_shortcodes_list()
    {
        return APVC_Shortcodes::apvc_list_shortcodes();
    }
    
    public static function get_settings_widget_one()
    {
        
        if ( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['apvc-basic-submit'] ) ) {
            $option_value = array();
            if ( isset( $_POST['apvc_enable_counter'] ) ) {
                $option_value['apvc_enable_counter'] = APVC_Admin_Helpers::sanitize_text_or_array_field( $_POST['apvc_enable_counter'] );
            }
            if ( isset( $_POST['apvc_post_types'] ) ) {
                $option_value['apvc_post_types'] = APVC_Admin_Helpers::sanitize_text_or_array_field( $_POST['apvc_post_types'] );
            }
            if ( isset( $_POST['apvc_exclude_users'] ) ) {
                $option_value['apvc_exclude_users'] = APVC_Admin_Helpers::sanitize_text_or_array_field( $_POST['apvc_exclude_users'] );
            }
            if ( isset( $_POST['apvc_exclude_posts'] ) ) {
                $option_value['apvc_exclude_posts'] = APVC_Admin_Helpers::sanitize_text_or_array_field( $_POST['apvc_exclude_posts'] );
            }
            if ( isset( $_POST['apvc_exclude_ips'] ) ) {
                $option_value['apvc_exclude_ips'] = APVC_Admin_Helpers::sanitize_text_or_array_field( APVC_Admin_Helpers::extract( $_POST['apvc_exclude_ips'] ) );
            }
            if ( is_array( $option_value ) && !empty($option_value) ) {
                update_option( 'apvc_basic_settings', $option_value );
            }
            echo  '<script>window.location.href = "' . APVC_DASHBOARD_SETTINGS_PAGE_LINK . '";</script>' ;
            exit;
        }
        
        
        if ( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['apvc-basic-reset'] ) ) {
            $option_value = array();
            $option_value['apvc_enable_counter'] = 'on';
            $option_value['apvc_post_types'] = array( 'post', 'page' );
            $option_value['apvc_exclude_users'] = array();
            $option_value['apvc_exclude_posts'] = array();
            $option_value['apvc_exclude_ips'] = array();
            if ( is_array( $option_value ) && !empty($option_value) ) {
                update_option( 'apvc_basic_settings', $option_value );
            }
            echo  '<script>window.location.href = "' . APVC_DASHBOARD_SETTINGS_PAGE_LINK . '";</script>' ;
            exit;
        }
        
        $saved_option = get_option( 'apvc_basic_settings', true );
        $enable_counter = $saved_option['apvc_enable_counter'] ?? 'off';
        ?>
		<div class="col-lg-6 col-md-12 mb-4 apvc-basic-settings" id="apvc-basic-settings">
			<div class="card mb-4">
				<h5 class="card-header"><?php 
        esc_html_e( 'Basic Settings', 'apvc' );
        ?></h5>
				<div class="card-body">
					<div class="row">
						<form action="" method="post">
						<div class="col-md-12 mb-4">
							<div class="form-check mt-3">
								<input class="form-check-input" type="checkbox" id="apvc-enable-counter" name="apvc_enable_counter" <?php 
        echo  APVC_Admin_Helpers::is_selected_checkbox( $enable_counter ?? '', 'on' ) ;
        ?> />
								<label class="form-check-label" for="apvc-enable-counter"><?php 
        esc_html_e( 'Enable Counter', 'apvc' );
        ?></label>
							</div>
						</div>
						<div class="col-md-12 mb-4">
							<label for="apvc-post-types" class="form-label"><?php 
        esc_html_e( 'Track Post Types', 'apvc' );
        ?></label>
							<div class="select2-primary">
								<select name="apvc_post_types[]" class="select2 form-select" multiple>
								<?php 
        $post_types = APVC_Admin_Helpers::get_post_types();
        $saved_post_types = ( isset( $saved_option['apvc_post_types'] ) ? $saved_option['apvc_post_types'] : array() );
        if ( !empty($post_types) ) {
            foreach ( $post_types as $key => $value ) {
                $selected = APVC_Admin_Helpers::is_selected_array( $key, $saved_post_types );
                echo  '<option value="' . $key . '" ' . $selected . '>' . ucfirst( $value ) . '</option>' ;
            }
        }
        ?>
								</select>
							</div>
						</div>

						<div class="col-md-12 mb-4">
							<label for="apvc_exclude_users" class="form-label"><?php 
        esc_html_e( 'Block Users', 'apvc' );
        ?></label>
							<div class="select2-primary">
								<select name="apvc_exclude_users[]" class="select2 form-select" multiple>
									<?php 
        $all_users = APVC_Admin_Helpers::get_all_users();
        $saved_all_users = ( isset( $saved_option['apvc_exclude_users'] ) ? $saved_option['apvc_exclude_users'] : array() );
        if ( !empty($all_users) ) {
            foreach ( $all_users as $value ) {
                $selected = APVC_Admin_Helpers::is_selected_array( $value->ID, $saved_all_users );
                echo  '<option value="' . $value->ID . '" ' . $selected . '>' . ucfirst( $value->display_name ) . '</option>' ;
            }
        }
        ?>
								</select>
							</div>
						</div>

						<div class="col-md-12 mb-4">
							<label for="apvc_exclude_posts" class="form-label"><?php 
        esc_html_e( 'Block Post/Pages Counts', 'apvc' );
        ?></label>
							<div class="select2-primary">
								<select name="apvc_exclude_posts[]" class="select2 form-select" multiple>
									<?php 
        $all_posts = APVC_Admin_Helpers::get_all_posts();
        $saved_all_posts = ( isset( $saved_option['apvc_exclude_posts'] ) ? $saved_option['apvc_exclude_posts'] : array() );
        if ( !empty($all_posts) ) {
            foreach ( $all_posts as $value ) {
                $selected = APVC_Admin_Helpers::is_selected_array( $value->ID, $saved_all_posts );
                echo  '<option value="' . $value->ID . '" ' . $selected . '>' . ucfirst( $value->post_type ) . ' : ' . ucfirst( $value->post_title ) . '</option>' ;
            }
        }
        ?>
								</select>
							</div>
						</div>

						<div class="col-md-12 mb-4">
							<div class="aselect2-primary">
								<?php 
        $decode = $saved_option['apvc_exclude_ips'];
        $existing_values = '';
        if ( !empty($decode[0]) ) {
            $existing_values = implode( ',', $decode );
        }
        ?>
								<label for="apvc_exclude_ips" class="form-label"><?php 
        esc_html_e( 'Block IP Addresses', 'apvc' );
        ?></label>
								<input id="apvc_exclude_ips" class="form-control" name="apvc_exclude_ips" value="<?php 
        echo  $existing_values ;
        ?>" />
							</div>
						</div>

						<div class="col-12 d-flex ">
							<button type="submit" name="apvc-basic-submit" class="btn btn-primary btn-submit m-2"><?php 
        esc_html_e( 'Save Settings', 'apvc' );
        ?></button>
							<button type="submit" name="apvc-basic-reset" class="btn btn-primary btn-reset m-2"><?php 
        esc_html_e( 'Reset Settings', 'apvc' );
        ?></button>
						</div>
						</form>
					</div>
				</div>
			</div>
			<?php 
        
        if ( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['apvc-widget-vis-btn'] ) ) {
            $option_value = array();
            if ( isset( $_POST['apvc_total_visits_cr_page'] ) ) {
                $option_value['apvc_total_visits_cr_page'] = APVC_Admin_Helpers::sanitize_text_or_array_field( $_POST['apvc_total_visits_cr_page'] );
            }
            if ( isset( $_POST['apvc_show_total_counts'] ) ) {
                $option_value['apvc_show_total_counts'] = APVC_Admin_Helpers::sanitize_text_or_array_field( $_POST['apvc_show_total_counts'] );
            }
            if ( isset( $_POST['apvc_show_today_counts'] ) ) {
                $option_value['apvc_show_today_counts'] = sanitize_text_or_array_field( $_POST['apvc_show_today_counts'] );
            }
            if ( is_array( $option_value ) && !empty($option_value) ) {
                update_option( 'apvc_widget_visibility_settings', $option_value );
            }
            echo  '<script>window.location.href = "' . esc_url( APVC_DASHBOARD_SETTINGS_PAGE_LINK ) . '";</script>' ;
            exit;
        }
        
        
        if ( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['apvc-widget-reset-vis-btn'] ) ) {
            $option_value = array();
            $option_value['apvc_total_visits_cr_page'] = '';
            $option_value['apvc_show_total_counts'] = '';
            $option_value['apvc_show_today_counts'] = 'on';
            if ( is_array( $option_value ) && !empty($option_value) ) {
                update_option( 'apvc_widget_visibility_settings', $option_value );
            }
            echo  '<script>window.location.href = "' . APVC_DASHBOARD_SETTINGS_PAGE_LINK . '";</script>' ;
            exit;
        }
        
        $saved_option = get_option( 'apvc_widget_visibility_settings', true );
        ?>
			<div class="card mb-4">
				<h5 class="card-header"><?php 
        esc_html_e( 'Widget Visibility Settings', 'apvc' );
        ?></h5>
				<div class="card-body">
					<div class="row">
						<form action="" method="post">
							<div class="col-md-12 mb-4">
								<div class="form-check mt-3">
									<input class="form-check-input" type="checkbox" <?php 
        echo  APVC_Admin_Helpers::is_selected_checkbox( $saved_option['apvc_total_visits_cr_page'] ?? '', 'on' ) ;
        ?> name="apvc_total_visits_cr_page" id="apvc_total_visits_cr_page" />
									<label class="form-check-label" for="apvc_total_visits_cr_page"><?php 
        esc_html_e( 'Show Total Visits of Current Page', 'apvc' );
        ?></label>
									<div class="form-text"><?php 
        esc_html_e( '*This will show total counts the current page.', 'apvc' );
        ?></div>
								</div>
							</div>
							<div class="col-md-12 mb-4">
								<div class="form-check mt-3">
									<input class="form-check-input" type="checkbox" name="apvc_show_total_counts" id="apvc_show_total_counts" <?php 
        echo  APVC_Admin_Helpers::is_selected_checkbox( $saved_option['apvc_show_total_counts'] ?? '', 'on' ) ;
        ?> />
									<label class="form-check-label" for="apvc_show_total_counts"><?php 
        esc_html_e( 'Show Global Total Counts', 'apvc' );
        ?></label>
									<div class="form-text"><?php 
        esc_html_e( '*This will show total counts for whole website.', 'apvc' );
        ?></div>
								</div>
							</div>

							<div class="col-md-12 mb-4">
								<div class="form-check mt-3">
									<input class="form-check-input" type="checkbox" name="apvc_show_today_counts" id="apvc_show_today_counts" <?php 
        echo  APVC_Admin_Helpers::is_selected_checkbox( $saved_option['apvc_show_today_counts'] ?? '', 'on' ) ;
        ?> />
									<label class="form-check-label" for="apvc_show_today_counts"><?php 
        esc_html_e( "Show Today's Counts", 'apvc' );
        ?></label>
									<div class="form-text"><?php 
        esc_html_e( '*This will show total counts for whole website.', 'apvc' );
        ?></div>
								</div>
							</div>

							<div class="col-12 d-flex">
								<button name="apvc-widget-vis-btn" type="submit" class="btn btn-primary btn-submit m-2"><?php 
        esc_html_e( 'Save Settings', 'apvc' );
        ?></button>
								<button type="submit" name="apvc-widget-reset-vis-btn" class="btn btn-primary btn-reset m-2"><?php 
        esc_html_e( 'Reset Settings', 'apvc' );
        ?></button>
							</div>
						</form>
					</div>
				</div>
			</div>

			<?php 
        
        if ( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['apvc-widget-tmpl-btn'] ) ) {
            $option_value = array();
            if ( isset( $_POST['apvc-widget-templates'] ) ) {
                $option_value['apvc_widget_templates'] = APVC_Admin_Helpers::sanitize_text_or_array_field( $_POST['apvc-widget-templates'] );
            }
            if ( is_array( $option_value ) && !empty($option_value) ) {
                update_option( 'apvc_widget_template_settings', $option_value );
            }
            echo  '<script>window.location.href = "' . APVC_DASHBOARD_SETTINGS_PAGE_LINK . '";</script>' ;
            exit;
        }
        
        
        if ( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['apvc-widget-reset-tmpl-btn'] ) ) {
            $option_value = array();
            $option_value['apvc_widget_templates'] = '-';
            if ( is_array( $option_value ) && !empty($option_value) ) {
                update_option( 'apvc_widget_template_settings', $option_value );
            }
            echo  '<script>window.location.href = "' . APVC_DASHBOARD_SETTINGS_PAGE_LINK . '";</script>' ;
            exit;
        }
        
        $saved_option = get_option( 'apvc_widget_template_settings', true );
        ?>

			<div class="card mb-4">
				<h5 class="card-header"><?php 
        esc_html_e( 'Widget Templates ', 'apvc' );
        ?></h5>
				<div class="card-body">
					<div class="row">
						<form action="" method="post">
							<div class="col-md-12 mb-4">
								<label for="apvc-widget-templates" class="form-label"><?php 
        esc_html_e( 'Widget Templates', 'apvc' );
        ?></label>
								<div class="select2-primary">
									<?php 
        $shortcodes = APVC_Shortcode_Templates::apvc_get_shortcode_templates();
        ?>
									<select name="apvc-widget-templates" class="select2 form-select">
										<option value="-"><?php 
        echo  esc_html_e( 'Please Select Template', 'apvc' ) ;
        ?></option>
										<?php 
        if ( !empty($shortcodes) && count( $shortcodes ) > 0 ) {
            foreach ( $shortcodes as $shortcode ) {
                echo  '<option value="' . $shortcode['key'] . '" ' . APVC_Admin_Helpers::is_selected_string( $shortcode['key'], $saved_option['apvc_widget_templates'] ) . '>' . $shortcode['name'] . '</option>' ;
            }
        }
        ?>
									</select>
								</div><br />
								<div class="form-text"><?php 
        esc_html_e( '*Check the Shortcode Library page to check the demo of all the shortcodes.', 'apvc' );
        ?></div>
								<div class="form-text"><?php 
        esc_html_e( '*All color properties ignored if any template selected.', 'apvc' );
        ?></div>
								<div class="form-text"><?php 
        esc_html_e( '*More than 40 templates available in the Premium version of the plugin.', 'apvc' );
        ?></div>
								<div class="form-text"><?php 
        esc_html_e( '*All widget settings will be not honoured if you selected the template.', 'apvc' );
        ?></div>
							</div>

							<div class="col-12 d-flex">
								<button name="apvc-widget-tmpl-btn" type="submit" class="btn btn-primary btn-submit m-2"><?php 
        esc_html_e( 'Save Settings', 'apvc' );
        ?></button>
								<button type="submit" name="apvc-widget-reset-tmpl-btn" class="btn btn-primary btn-reset m-2"><?php 
        esc_html_e( 'Reset Settings', 'apvc' );
        ?></button>
							</div>
						</form>
					</div>
				</div>
			</div>
			<?php 
        
        if ( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['apvc-advanced-settings-btn'] ) ) {
            $option_value = array();
            
            if ( !empty($_POST['apvc_show_shorthand_counter']) ) {
                $option_value['apvc_show_shorthand_counter'] = APVC_Admin_Helpers::sanitize_text_or_array_field( $_POST['apvc_show_shorthand_counter'] );
            } else {
                $option_value['apvc_show_shorthand_counter'] = '';
            }
            
            
            if ( !empty($_POST['apvc_track_loggedin_users']) ) {
                $option_value['apvc_track_loggedin_users'] = APVC_Admin_Helpers::sanitize_text_or_array_field( $_POST['apvc_track_loggedin_users'] );
            } else {
                $option_value['apvc_track_loggedin_users'] = '';
            }
            
            if ( is_array( $option_value ) && !empty($option_value) ) {
                update_option( 'apvc_advanced_settings', $option_value );
            }
            echo  '<script>window.location.href = "' . APVC_DASHBOARD_SETTINGS_PAGE_LINK . '";</script>' ;
            exit;
        }
        
        
        if ( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['apvc-advanced-reset-settings-btn'] ) ) {
            $option_value = array();
            $option_value['apvc_show_shorthand_counter'] = '';
            $option_value['apvc_track_loggedin_users'] = '';
            if ( is_array( $option_value ) && !empty($option_value) ) {
                update_option( 'apvc_advanced_settings', $option_value );
            }
            echo  '<script>window.location.href = "' . APVC_DASHBOARD_SETTINGS_PAGE_LINK . '";</script>' ;
            exit;
        }
        
        $saved_option = get_option( 'apvc_advanced_settings', true );
        ?>
			<div class="card mb-4">
				<h5 class="card-header"><?php 
        esc_html_e( 'Advanced Settings', 'apvc' );
        ?></h5>
				<div class="card-body">
					<div class="row">
						<form action="" method="post">
							<div class="col-md-12 mb-4">
								<div class="form-check mt-3">
									<input class="form-check-input" type="checkbox" <?php 
        echo  APVC_Admin_Helpers::is_selected_checkbox( $saved_option['apvc_show_shorthand_counter'], 'on' ) ;
        ?>  name="apvc_show_shorthand_counter" id="apvc_show_shorthand_counter"/>
									<label class="form-check-label" for="apvc_show_shorthand_counter"><?php 
        esc_html_e( 'Show number in Short Version eg: 1000 -> 1k:', 'apvc' );
        ?></label>
								</div>
							</div>

							<div class="col-md-12 mb-4">
								<div class="form-check mt-3">
									<input class="form-check-input" type="checkbox" <?php 
        echo  APVC_Admin_Helpers::is_selected_checkbox( $saved_option['apvc_track_loggedin_users'], 'on' ) ;
        ?> name="apvc_track_loggedin_users" id="apvc_track_loggedin_users"/>
									<label class="form-check-label" for="apvc_track_loggedin_users"><?php 
        esc_html_e( 'Track logged in users only', 'apvc' );
        ?></label>
								</div>
							</div>

							<div class="col-12 d-flex">
								<button class="btn btn-primary btn-submit m-2" name="apvc-advanced-settings-btn" type="submit"><?php 
        esc_html_e( 'Save Settings', 'apvc' );
        ?></button>
								<button type="submit" name="apvc-advanced-reset-settings-btn" class="btn btn-primary btn-reset m-2"><?php 
        esc_html_e( 'Reset Settings', 'apvc' );
        ?></button>
							</div>
						</form>
					</div>
				</div>
			</div>


		</div>
		<?php 
    }
    
    public static function get_settings_widgets_two()
    {
        
        if ( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['apvc-widget-set-btn'] ) ) {
            $option_value = array();
            if ( isset( $_POST['apvc_show_icon'] ) ) {
                $option_value['apvc_show_icon'] = APVC_Admin_Helpers::sanitize_text_or_array_field( $_POST['apvc_show_icon'] );
            }
            if ( isset( $_POST['apvc-widget-display'] ) ) {
                $option_value['apvc_widget_display'] = APVC_Admin_Helpers::sanitize_text_or_array_field( $_POST['apvc-widget-display'] );
            }
            if ( isset( $_POST['apvc-default-counter-text-color'] ) ) {
                $option_value['apvc_default_counter_text_color'] = APVC_Admin_Helpers::sanitize_text_or_array_field( $_POST['apvc-default-counter-text-color'] );
            }
            if ( isset( $_POST['apvc-default-counter-border-color'] ) ) {
                $option_value['apvc_default_counter_border_color'] = APVC_Admin_Helpers::sanitize_text_or_array_field( $_POST['apvc-default-counter-border-color'] );
            }
            if ( isset( $_POST['apvc-default-background-color'] ) ) {
                $option_value['apvc_default_background_color'] = APVC_Admin_Helpers::sanitize_text_or_array_field( $_POST['apvc-default-background-color'] );
            }
            if ( isset( $_POST['apvc-default-border-radius'] ) ) {
                $option_value['apvc_default_border_radius'] = APVC_Admin_Helpers::sanitize_text_or_array_field( $_POST['apvc-default-border-radius'] );
            }
            if ( isset( $_POST['apvc-default-border-style'] ) ) {
                $option_value['apvc_default_border_style'] = APVC_Admin_Helpers::sanitize_text_or_array_field( $_POST['apvc-default-border-style'] );
            }
            if ( isset( $_POST['apvc-default-font-style'] ) ) {
                $option_value['apvc_default_font_style'] = APVC_Admin_Helpers::sanitize_text_or_array_field( $_POST['apvc-default-font-style'] );
            }
            if ( isset( $_POST['apvc-default-border-width'] ) ) {
                $option_value['apvc_default_border_width'] = APVC_Admin_Helpers::sanitize_text_or_array_field( $_POST['apvc-default-border-width'] );
            }
            if ( isset( $_POST['apvc-widget-display-fe'] ) ) {
                $option_value['apvc_widget_display_fe'] = APVC_Admin_Helpers::sanitize_text_or_array_field( $_POST['apvc-widget-display-fe'] );
            }
            if ( isset( $_POST['apvc-widget-width'] ) ) {
                $option_value['apvc_widget_width'] = APVC_Admin_Helpers::sanitize_text_or_array_field( $_POST['apvc-widget-width'] );
            }
            if ( isset( $_POST['apvc-widget-padding'] ) ) {
                $option_value['apvc_widget_padding'] = APVC_Admin_Helpers::sanitize_text_or_array_field( $_POST['apvc-widget-padding'] );
            }
            if ( isset( $_POST['apvc-default-label'] ) ) {
                $option_value['apvc_default_label'] = stripslashes( APVC_Admin_Helpers::sanitize_text_or_array_field( $_POST['apvc-default-label'] ) );
            }
            if ( isset( $_POST['apvc-today-label'] ) ) {
                $option_value['apvc_today_label'] = stripslashes( APVC_Admin_Helpers::sanitize_text_or_array_field( $_POST['apvc-today-label'] ) );
            }
            if ( isset( $_POST['apvc-total-counts-label'] ) ) {
                $option_value['apvc_total_counts_label'] = stripslashes( APVC_Admin_Helpers::sanitize_text_or_array_field( $_POST['apvc-total-counts-label'] ) );
            }
            if ( is_array( $option_value ) && !empty($option_value) ) {
                update_option( 'apvc_widget_settings', $option_value );
            }
            echo  '<script>window.location.href = "' . APVC_DASHBOARD_SETTINGS_PAGE_LINK . '";</script>' ;
            exit;
        }
        
        
        if ( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['apvc-widget-reset'] ) ) {
            $option_value = array();
            $option_value['apvc_show_icon'] = 'on';
            $option_value['apvc_widget_display'] = 'disable';
            $option_value['apvc_default_counter_text_color'] = '#000000';
            $option_value['apvc_default_counter_border_color'] = '#000000';
            $option_value['apvc_default_background_color'] = '#E5E5E59E';
            $option_value['apvc_default_border_radius'] = 5;
            $option_value['apvc_default_border_style'] = 'solid';
            $option_value['apvc_default_font_style'] = 'normal';
            $option_value['apvc_default_border_width'] = 2;
            $option_value['apvc_widget_display_fe'] = 'center';
            $option_value['apvc_widget_width'] = 300;
            $option_value['apvc_widget_padding'] = 5;
            $option_value['apvc_default_label'] = __( 'Total Visits:', 'apvc' );
            $option_value['apvc_today_label'] = __( "Today's Visits:", 'apvc' );
            $option_value['apvc_total_counts_label'] = __( 'All time total visits:', 'apvc' );
            if ( is_array( $option_value ) && !empty($option_value) ) {
                update_option( 'apvc_widget_settings', $option_value );
            }
            echo  '<script>window.location.href = "' . APVC_DASHBOARD_SETTINGS_PAGE_LINK . '";</script>' ;
            exit;
        }
        
        $saved_option = get_option( 'apvc_widget_settings', true );
        ?>
		<div class="col-lg-6 col-md-12 mb-2 apvc-widget-settings" id="apvc-widget-settings">
			<div class="card mb-4">
				<h5 class="card-header"><?php 
        esc_html_e( 'Widget Settings', 'apvc' );
        ?></h5>
				<div class="card-body">
					<div class="row">
						<form action="" method="post">

						<div class="col-md-12 mb-4">
							<div class="form-check mt-3">
								<input class="form-check-input" type="checkbox" <?php 
        echo  APVC_Admin_Helpers::is_selected_checkbox( $saved_option['apvc_show_icon'] ?? '', 'on' ) ;
        ?> name="apvc_show_icon" id="apvc_show_icon" />
								<label class="form-check-label" for="apvc_show_icon"><?php 
        esc_html_e( 'Show Icon', 'apvc' );
        ?></label>
							</div>
						</div>

						<div class="col-md-12 mb-4">
							<label for="apvc-widget-display" class="form-label"><?php 
        esc_html_e( 'Show Counter on Front End', 'apvc' );
        ?></label>
							<div class="select2-primary">
								<select name="apvc-widget-display" class="select2 form-select">
									<option value="disable" <?php 
        echo  APVC_Admin_Helpers::is_selected_string( 'disable', $saved_option['apvc_widget_display'] ) ;
        ?>><?php 
        esc_html_e( 'Hide', 'apvc' );
        ?></option>
									<option value="above_the_content" <?php 
        echo  APVC_Admin_Helpers::is_selected_string( 'above_the_content', $saved_option['apvc_widget_display'] ) ;
        ?>><?php 
        esc_html_e( 'Above the content', 'apvc' );
        ?></option>
									<option value="below_the_content" <?php 
        echo  APVC_Admin_Helpers::is_selected_string( 'below_the_content', $saved_option['apvc_widget_display'] ) ;
        ?>><?php 
        esc_html_e( 'Below the content', 'apvc' );
        ?></option>
								</select>
							</div>
						</div>

						<div class="col-md-12 mb-4">
							<label for="apvc-widget-display" class="form-label"><?php 
        esc_html_e( 'Default Counter Text Color', 'apvc' );
        ?></label>
							<div class="classic col col-sm-3 col-lg-2">
								<div id="apvc-default-counter-text-color"></div>
								<input type="hidden" name="apvc-default-counter-text-color" value="<?php 
        echo  APVC_Admin_Helpers::is_isset_value( $saved_option['apvc_default_counter_text_color'], '#004AAD' ) ;
        ?>">
							</div>
						</div>

						<div class="col-md-12 mb-4">
							<label for="apvc-widget-display" class="form-label"><?php 
        esc_html_e( 'Default Counter Border Color', 'apvc' );
        ?></label>
							<div class="classic col col-sm-3 col-lg-2">
								<div id="apvc-default-counter-border-color"></div>
								<input type="hidden" name="apvc-default-counter-border-color" value="<?php 
        echo  APVC_Admin_Helpers::is_isset_value( $saved_option['apvc_default_counter_border_color'], '#0969E7' ) ;
        ?>">
							</div>
						</div>

						<div class="col-md-12 mb-4">
							<label for="apvc-widget-display" class="form-label"><?php 
        esc_html_e( 'Default Background Color', 'apvc' );
        ?></label>
							<div class="classic col col-sm-3 col-lg-2">
								<div id="apvc-default-background-color"></div>
								<input type="hidden" name="apvc-default-background-color" value="<?php 
        echo  APVC_Admin_Helpers::is_isset_value( $saved_option['apvc_default_background_color'], '#F0FFC4' ) ;
        ?>">
							</div>
						</div>

						<div class="col-md-12 mb-4">
							<label for="apvc-default-border-radius" class="form-label"><?php 
        esc_html_e( 'Default Border Radius', 'apvc' );
        ?></label>
							<input type="number" min="0" value="5" max="20" class="form-control" name="apvc-default-border-radius" value="<?php 
        echo  APVC_Admin_Helpers::is_isset_value( $saved_option['apvc_default_border_radius'], 5 ) ;
        ?>">
						</div>

						<div class="col-md-12 mb-4">
							<label for="apvc-border-style" class="form-label"><?php 
        esc_html_e( 'Border Style', 'apvc' );
        ?></label>
							<select name="apvc-default-border-style" class="form-select" style="max-width: 100%;">
								<option value="none" <?php 
        echo  APVC_Admin_Helpers::is_selected_string( $saved_option['apvc_default_border_style'], 'none' ) ;
        ?>><?php 
        echo  esc_html_e( 'None', 'apvc' ) ;
        ?></option>
								<option value="dotted" <?php 
        echo  APVC_Admin_Helpers::is_selected_string( $saved_option['apvc_default_border_style'], 'dotted' ) ;
        ?>><?php 
        echo  esc_html_e( 'Dotted', 'apvc' ) ;
        ?></option>
								<option value="dashed" <?php 
        echo  APVC_Admin_Helpers::is_selected_string( $saved_option['apvc_default_border_style'], 'dashed' ) ;
        ?>><?php 
        echo  esc_html_e( 'Dashed', 'apvc' ) ;
        ?></option>
								<option value="solid" <?php 
        echo  APVC_Admin_Helpers::is_selected_string( $saved_option['apvc_default_border_style'], 'solid' ) ;
        ?>><?php 
        echo  esc_html_e( 'Solid', 'apvc' ) ;
        ?></option>
								<option value="double" <?php 
        echo  APVC_Admin_Helpers::is_selected_string( $saved_option['apvc_default_border_style'], 'double' ) ;
        ?>><?php 
        echo  esc_html_e( 'Double', 'apvc' ) ;
        ?></option>
							</select>
						</div>

						<div class="col-md-12 mb-4">
							<label for="apvc-default-font-style" class="form-label"><?php 
        esc_html_e( 'Font Style', 'apvc' );
        ?></label>
							<select name="apvc-default-font-style" class="form-select" style="max-width: 100%;">
								<option value="normal" <?php 
        echo  APVC_Admin_Helpers::is_selected_string( $saved_option['apvc_default_font_style'], 'normal' ) ;
        ?>><?php 
        echo  esc_html_e( 'Normal', 'apvc' ) ;
        ?></option>
								<option value="bold" <?php 
        echo  APVC_Admin_Helpers::is_selected_string( $saved_option['apvc_default_font_style'], 'bold' ) ;
        ?>><?php 
        echo  esc_html_e( 'Bold', 'apvc' ) ;
        ?></option>
								<option value="italic" <?php 
        echo  APVC_Admin_Helpers::is_selected_string( $saved_option['apvc_default_font_style'], 'italic' ) ;
        ?>><?php 
        echo  esc_html_e( 'Italic', 'apvc' ) ;
        ?></option>
							</select>
						</div>

						<div class="col-md-12 mb-4">
							<label for="apvc-default-border-width" class="form-label"><?php 
        esc_html_e( 'Default Border Width', 'apvc' );
        ?></label>
							<input type="number" min="0" value="2" max="20" class="form-control" name="apvc-default-border-width" value="<?php 
        echo  APVC_Admin_Helpers::is_isset_value( $saved_option['apvc_default_border_width'], 2 ) ;
        ?>">
						</div>

						<div class="col-md-12 mb-4">
							<label for="apvc-widget-display-fe" class="form-label"><?php 
        esc_html_e( 'Show Counter on Front End', 'apvc' );
        ?></label>
							<div class="select">
								<select id="apvc-widget-display-fe" name="apvc-widget-display-fe" class="select2 form-select">
									<option value="left" <?php 
        echo  APVC_Admin_Helpers::is_selected_string( $saved_option['apvc_widget_display_fe'], 'left' ) ;
        ?>><?php 
        esc_html_e( 'Left', 'apvc' );
        ?></option>
									<option value="right" <?php 
        echo  APVC_Admin_Helpers::is_selected_string( $saved_option['apvc_widget_display_fe'], 'right' ) ;
        ?>><?php 
        esc_html_e( 'Right', 'apvc' );
        ?></option>
									<option value="center" <?php 
        echo  APVC_Admin_Helpers::is_selected_string( $saved_option['apvc_widget_display_fe'], 'center' ) ;
        ?>><?php 
        esc_html_e( 'Center', 'apvc' );
        ?></option>
								</select>
							</div>
						</div>

						<div class="col-md-12 mb-4">
							<label for="apvc-widget-width" class="form-label"><?php 
        esc_html_e( 'Width of the Widget (In Pixels)', 'apvc' );
        ?></label>
							<input type="number" min="10" max="5000" class="form-control" name="apvc-widget-width" value="<?php 
        echo  APVC_Admin_Helpers::is_isset_value( $saved_option['apvc_widget_width'], 300 ) ;
        ?>">
						</div>

						<div class="col-md-12 mb-4">
							<label for="apvc-widget-padding" class="form-label"><?php 
        esc_html_e( 'Padding of the Widget (In Pixels)', 'apvc' );
        ?></label>
							<input type="number" min="0" max="1000" class="form-control" name="apvc-widget-padding" value="<?php 
        echo  APVC_Admin_Helpers::is_isset_value( $saved_option['apvc_widget_padding'], 10 ) ;
        ?>">
						</div>

						<div class="col-md-12 mb-4">
							<label for="apvc-default-label" class="form-label"><?php 
        esc_html_e( 'Default Label (Total Visits of Current Page)', 'apvc' );
        ?></label>
							<input type="text" class="form-control" name="apvc-default-label" value="<?php 
        echo  APVC_Admin_Helpers::is_isset_value( $saved_option['apvc_default_label'], __( 'Total Visits of the page:', 'apvc' ) ) ;
        ?>">
						</div>

						<div class="col-md-12 mb-4">
							<label for="apvc-today-label" class="form-label"><?php 
        esc_html_e( "Today's Count Label", 'apvc' );
        ?></label>
							<input type="text" class="form-control" name="apvc-today-label" value="<?php 
        echo  APVC_Admin_Helpers::is_isset_value( $saved_option['apvc_today_label'], __( "Today's visits:", 'apvc' ) ) ;
        ?>">
						</div>

						<div class="col-md-12 mb-4">
							<label for="apvc-total-counts-label" class="form-label"><?php 
        esc_html_e( 'Total Counts Label (Global)', 'apvc' );
        ?></label>
							<input type="text" class="form-control" name="apvc-total-counts-label" value="<?php 
        echo  APVC_Admin_Helpers::is_isset_value( $saved_option['apvc_total_counts_label'], __( 'Total Visits:', 'apvc' ) ) ;
        ?>">
						</div>

						<div class="col-12 d-flex">
							<button type="submit" name="apvc-widget-set-btn" class="btn btn-primary btn-submit  m-2"><?php 
        esc_html_e( 'Save Settings', 'apvc' );
        ?></button>
							<button type="submit" name="apvc-widget-reset" class="btn btn-primary btn-reset m-2"><?php 
        esc_html_e( 'Reset Settings', 'apvc' );
        ?></button>
						</div>
						</form>
					</div>
				</div>
			</div>

			<?php 
        
        if ( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['apvc-update-geo-data'] ) ) {
            $geo_database = new APVC_Geo_Database();
            $geo_database->maybe_dispatch_download_job();
            echo  '<script>window.location.href = "' . APVC_DASHBOARD_SETTINGS_PAGE_LINK . '";</script>' ;
            exit;
        }
        
        $db_data = get_option( 'apvc_geo_database_date', 0 );
        ?>
			<div class="card mb-4">
				<h5 class="card-header"><?php 
        esc_html_e( 'Geographic Database File', 'apvc' );
        ?></h5>
				<div class="card-body">
					<div class="row">
						<form action="" id="reset_settings" method="post">
							<?php 
        
        if ( 0 === $db_data ) {
            ?>
								<label class="form-check-label" for=""><?php 
            echo  sprintf( __( 'Database file is not available on your server, Click to download latest file.' ), APVC_Helper_Functions::get_geo_data_status() ) ;
            ?></label>
							<?php 
        } else {
            ?>
								<label class="form-check-label" for=""><?php 
            echo  sprintf( __( 'Your geographic data updated %s days ago.' ), APVC_Helper_Functions::get_geo_data_status() ) ;
            ?></label>
							<?php 
        }
        
        ?>
							<div class="col-12 d-flex">
								<button type="submit" id="update_geo_data" name="apvc-update-geo-data" class="btn btn-primary btn-submit"><?php 
        esc_html_e( 'Update geographic data manually', 'apvc' );
        ?></button>
							</div>
							<div class="form-text"><?php 
        esc_html_e( '*Geographic database will be updated every 2 weeks automatically.', 'apvc' );
        ?></div>
						</form>
					</div>
				</div>
			</div>

			<div class="card mb-4">
				<h5 class="card-header"><?php 
        esc_html_e( 'Danger Zone', 'apvc' );
        ?></h5>
				<div class="card-body">
					<div class="row">
						<form action="" id="reset_settings" method="post">
							<div class="col-12 d-flex ">
								<button type="button" id="confirm-text" name="apvc-reset-submit" class="btn btn-danger btn-submit m-2"><?php 
        esc_html_e( 'Delete All Data', 'apvc' );
        ?></button>
							</div>
						</form>
					</div>
				</div>
			</div>

		</div>

				<?php 
    }
    
    public static function get_advanced_filters( $identifier )
    {
        
        if ( false === APVC_Admin_Helpers::check_page( 'advanced-page-visit-counter-dashboard' ) ) {
            ?>
			<div class="accordion accordion-header-primary" id="apvc_advanced_filters">
				<div class="accordion-item card">
					<h2 class="accordion-header">
						<button type="button" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#apvc-advanced-filters" aria-expanded="false">
							<?php 
            esc_html_e( 'Advanced Filters', 'apvc' );
            ?>&nbsp;&nbsp;<i class="bx bx-calendar"></i>
						</button>
					</h2>
					<div id="apvc-advanced-filters" class="accordion-collapse collapse" data-bs-parent="#apvc-advanced-filters" style="">
						<div class="accordion-body">
							<div class="col-4">
								<label for="bs-rangepicker-range" class="form-label"><?php 
            esc_html_e( 'Date Ranges', 'apvc' );
            ?></label>
								<input type="text" id="bs-rangepicker-range" class="form-control" />
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php 
        }
    
    }
    
    public static function get_advanced_filters_for_reports( $helpers, $args )
    {
        ?>
		<input type="hidden" value="reports" id="apvc_page_id">
		<div class="accordion accordion-header-primary" id="apvc_advanced_filters">
			<div class="accordion-item card">
				<h2 class="accordion-header">
					<button type="button" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#apvc-advanced-filters" aria-expanded="false">
						<?php 
        esc_html_e( 'Advanced Filters', 'apvc' );
        ?>&nbsp;&nbsp;<i class="bx bx-calendar"></i>
					</button>
				</h2>
				<div id="apvc-advanced-filters" class="accordion-collapse collapse 
				<?php 
        if ( isset( $args['set_filter'] ) && 'true' === $args['set_filter'] ) {
            echo  'show' ;
        }
        ?>
				" data-bs-parent="#apvc-advanced-filters" style="">
					<div class="accordion-body">
						<form action="" method="" id="apvc_filters_form">
						<div class="col-12">
							<?php 
        
        if ( isset( $args['type'] ) && 'page_list' === $args['type'] ) {
            ?>
								<input type="hidden" id="page_type" value="page_list">
							<div class="row">
								<div class="col-4">
									<?php 
            $args = array(
                'public' => true,
            );
            $post_types = get_post_types( $args );
            ?>
									<label for="apvc_list_post_types" class="form-label"><?php 
            esc_html_e( 'Post Type', 'apvc' );
            ?></label>
									<select id="apvc_list_post_types" class="form-select">
										<option value=""><?php 
            echo  esc_html_e( 'Please Select', 'apvc' ) ;
            ?></option>
										<?php 
            foreach ( $post_types as $key => $value ) {
                
                if ( isset( $args['post_type'] ) && $args['post_type'] === $key ) {
                    $selected = 'selected';
                } else {
                    $selected = '';
                }
                
                echo  '<option ' . $selected . '>' . $value . '</option>' ;
            }
            ?>
									</select>
								</div>
								<div class="col-4">
									<button type="submit" class="btn rounded-pill btn-primary" id="advanced_filters_pl"><?php 
            echo  esc_html_e( 'Submit', 'apvc' ) ;
            ?></button>
									<a href="<?php 
            echo  APVC_DASHBOARD_PAGE_LINK ;
            ?>&section=apvc-reports" class="btn rounded-pill btn-primary" id="advanced_filters_reset"><?php 
            echo  esc_html_e( 'Reset', 'apvc' ) ;
            ?></a>
								</div>
							</div>
									<?php 
        } else {
            ?>
							<div class="row">
								<div class="col-2">
									<label for="bs-rangepicker-range" class="form-label"><?php 
            esc_html_e( 'Date Ranges', 'apvc' );
            ?></label>
									<input type="text" id="bs-rangepicker-range" class="form-control" />
								</div>
								<div class="col-2">
									<?php 
            $devices = $helpers::get_device_type_list();
            ?>
									<label for="bs-rangepicker-range" class="form-label"><?php 
            esc_html_e( 'Device Type', 'apvc' );
            ?></label>
									<select id="apvc_rp_device_type" class="form-select">
										<option value=""><?php 
            echo  esc_html_e( 'Please Select', 'apvc' ) ;
            ?></option>
									<?php 
            foreach ( $devices as $device ) {
                
                if ( isset( $args['device_type'] ) && $args['device_type'] === $device->device_type ) {
                    $selected = 'selected';
                } else {
                    $selected = '';
                }
                
                echo  '<option ' . $selected . '>' . $device->device_type . '</option>' ;
            }
            ?>
									</select>
								</div>
								<div class="col-2">
									<?php 
            $oss = $helpers::get_device_os_list();
            ?>
									<label for="bs-rangepicker-range" class="form-label"><?php 
            esc_html_e( 'Device OS', 'apvc' );
            ?></label>
									<select id="apvc_rp_device_os" class="form-select">
										<option value=""><?php 
            echo  esc_html_e( 'Please Select', 'apvc' ) ;
            ?></option>
										<?php 
            foreach ( $oss as $os ) {
                
                if ( isset( $args['device_os'] ) && $args['device_os'] === $os->device_os ) {
                    $selected = 'selected';
                } else {
                    $selected = '';
                }
                
                echo  '<option ' . $selected . '>' . $os->device_os . '</option>' ;
            }
            ?>
									</select>
								</div>
								<div class="col-2">
									<?php 
            $brs = $helpers::get_device_br_list();
            ?>
									<label for="bs-rangepicker-range" class="form-label"><?php 
            esc_html_e( 'Device Browser', 'apvc' );
            ?></label>
									<select id="apvc_rp_device_br" class="form-select">
										<option value=""><?php 
            echo  esc_html_e( 'Please Select', 'apvc' ) ;
            ?></option>
										<?php 
            foreach ( $brs as $br ) {
                
                if ( isset( $args['device_br'] ) && $args['device_br'] === $br->device_browser ) {
                    $selected = 'selected';
                } else {
                    $selected = '';
                }
                
                echo  '<option ' . $selected . '>' . $br->device_browser . '</option>' ;
            }
            ?>
									</select>
								</div>
								<div class="col-4">
									<button type="submit" class="btn rounded-pill btn-primary" id="advanced_filters_rp"><?php 
            echo  esc_html_e( 'Submit', 'apvc' ) ;
            ?></button>
									<a href="<?php 
            echo  APVC_DASHBOARD_PAGE_LINK ;
            ?>&section=apvc-reports&sub_section=reports" class="btn rounded-pill btn-primary" id="advanced_filters_reset"><?php 
            echo  esc_html_e( 'Reset', 'apvc' ) ;
            ?></a>
								</div>
							</div>
							<?php 
        }
        
        ?>
						</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<?php 
    }
    
    public static function get_reports_page()
    {
        
        if ( isset( $_GET['sub_section'] ) && isset( $_GET['p_id'] ) ) {
            $helpers = new APVC_Admin_Helpers();
            $args = array();
            
            if ( $helpers::get_parameter( 'startDate' ) ) {
                $args['startDate'] = $helpers::get_parameter( 'startDate' );
                $args['endDate'] = $helpers::get_parameter( 'endDate' );
            }
            
            if ( $helpers::get_parameter( 'device_type' ) ) {
                $args['device_type'] = $helpers::get_parameter( 'device_type' );
            }
            if ( $helpers::get_parameter( 'device_os' ) ) {
                $args['device_os'] = $helpers::get_parameter( 'device_os' );
            }
            if ( $helpers::get_parameter( 'device_br' ) ) {
                $args['device_br'] = $helpers::get_parameter( 'device_br' );
            }
            if ( $helpers::get_parameter( 'set_filter' ) ) {
                $args['set_filter'] = $helpers::get_parameter( 'set_filter' );
            }
            if ( $helpers::get_parameter( 'p_id' ) ) {
                $args['p_id'] = $helpers::get_parameter( 'p_id' );
            }
            $startDate = ( isset( $args['startDate'] ) ?: '' );
            $endDate = ( isset( $args['endDate'] ) ?: '' );
            $device_type = ( isset( $args['device_type'] ) ?: '' );
            $device_os = ( isset( $args['device_os'] ) ?: '' );
            $device_br = ( isset( $args['device_br'] ) ?: '' );
            $allDetailedPosts = APVC_Query::get_detailed_reports( $args, 10 );
            ?>
			<div class="container-xxl flex-grow-1 container-p-y apvc-reports-block p-0 m-0">
				<?php 
            self::get_advanced_filters_for_reports( $helpers, $args );
            ?>
				<div class="card">
					<div class="table-responsive text-nowrap pt-4 pb-10">
						<input type="hidden" id="apvc-reports-pageid" value="<?php 
            echo  APVC_Helper_Functions::get_parameter( 'p_id' ) ;
            ?>">
						<input type="hidden" id="apvc-reports-off" value="10">
						<input type="hidden" id="apvc-reports-pagi-end" value="0">
						<input type="hidden" id="startDate" value="<?php 
            echo  $startDate ;
            ?>">
						<input type="hidden" id="endDate" value="<?php 
            echo  $endDate ;
            ?>">
						<input type="hidden" id="device_type" value="<?php 
            echo  $device_type ;
            ?>">
						<input type="hidden" id="device_os" value="<?php 
            echo  $device_os ;
            ?>">
						<input type="hidden" id="device_br" value="<?php 
            echo  $device_br ;
            ?>">
						<table class="table">
							<thead>
							<tr>
								<th><?php 
            esc_html_e( 'Page ID', 'apvc' );
            ?></th>
								<th><?php 
            esc_html_e( 'Page Title', 'apvc' );
            ?></th>
								<th><?php 
            esc_html_e( 'Post Type', 'apvc' );
            ?></th>
								<th><?php 
            esc_html_e( 'Viewed at', 'apvc' );
            ?></th>
								<th><?php 
            esc_html_e( 'Device Details', 'apvc' );
            ?></th>
								<th><?php 
            esc_html_e( 'Geo. Details', 'apvc' );
            ?></th>
							</tr>
							</thead>
							<tbody class="table-border-bottom-0 reports-table-body">
							<?php 
            
            if ( !empty($allDetailedPosts) ) {
                foreach ( $allDetailedPosts as $post ) {
                    ?>
									<tr>
										<td><?php 
                    echo  $post->singular_id ;
                    ?></td>
										<td><?php 
                    echo  ( 45 < strlen( $post->stored_title ) ? substr( $post->stored_title, 0, 45 ) . '...' : $post->stored_title ) ;
                    ?> <a href="<?php 
                    echo  $post->stored_url ;
                    ?>" target="_blank">  <i class='bx bx-link-external'></i></a></td>
										<td><?php 
                    echo  $post->stored_type ;
                    ?></td>
										<td><?php 
                    echo  $post->viewed_at ;
                    ?></td>
										<td>
                                            <b><?php 
                    esc_html_e( 'Type:', 'apvc' );
                    ?></b> <?php 
                    echo  $post->device_type ;
                    ?><br />
                                            <b><?php 
                    esc_html_e( 'OS:', 'apvc' );
                    ?></b> <?php 
                    echo  $post->device_os ;
                    ?><br />
                                            <b><?php 
                    esc_html_e( 'Browser:', 'apvc' );
                    ?></b> <?php 
                    echo  $post->device_browser ;
                    ?><br />
                                            <b><?php 
                    esc_html_e( 'Browser Ver.:', 'apvc' );
                    ?></b> <?php 
                    echo  $post->device_browser_ver ;
                    ?><br />
                                        </td>
										<td>
											<b><?php 
                    esc_html_e( 'City:', 'apvc' );
                    ?></b> <?php 
                    echo  $post->city ;
                    ?><br />
											<b><?php 
                    esc_html_e( 'State:', 'apvc' );
                    ?></b> <?php 
                    echo  $post->state_name ;
                    ?><br />
											<b><?php 
                    esc_html_e( 'Country:', 'apvc' );
                    ?></b> <?php 
                    echo  $post->country ;
                    ?><br />
										</td>
									</tr>
									<?php 
                }
            } else {
                ?>
								<tr>
									<td colspan="9" class="text-center pt-5"><h5><?php 
                esc_html_e( 'No posts found!', 'apvc' );
                ?></h5></td>
								</tr>
							<?php 
            }
            
            ?>
							</tbody>
						</table>
					</div>
					<div class="row py-sm-4 gy-3 gy-sm-0 ">
						<div class="col apvc-load-more-reports">
							<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin:auto;background:#fff;display:block;" width="75px" height="200px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
								<defs>
									<clipPath id="progress-y7p5500zcuj-cp" x="0" y="0" width="100" height="100">
										<rect x="0" y="0" width="0" height="100">
											<animate attributeName="width" repeatCount="indefinite" dur="1s" values="0;100;100" keyTimes="0;0.5;1"></animate>
											<animate attributeName="x" repeatCount="indefinite" dur="1s" values="0;0;100" keyTimes="0;0.5;1"></animate>
										</rect>
									</clipPath>
								</defs>
								<path fill="none" stroke="#5a8dee" stroke-width="2.79" d="M18 36.895L81.99999999999999 36.895A13.104999999999999 13.104999999999999 0 0 1 95.10499999999999 50L95.10499999999999 50A13.104999999999999 13.104999999999999 0 0 1 81.99999999999999 63.105L18 63.105A13.104999999999999 13.104999999999999 0 0 1 4.895000000000003 50L4.895000000000003 50A13.104999999999999 13.104999999999999 0 0 1 18 36.895 Z"></path>
								<path fill="#6610f2" clip-path="url(#progress-y7p5500zcuj-cp)" d="M18 40.99L82 40.99A9.009999999999998 9.009999999999998 0 0 1 91.00999999999999 50L91.00999999999999 50A9.009999999999998 9.009999999999998 0 0 1 82 59.01L18 59.01A9.009999999999998 9.009999999999998 0 0 1 8.990000000000004 50L8.990000000000004 50A9.009999999999998 9.009999999999998 0 0 1 18 40.99 Z"></path>
							</svg>
						</div>
					</div>
				</div>
			</div>
			<?php 
        } else {
            $helpers = new APVC_Admin_Helpers();
            $args = array();
            $args['type'] = 'page_list';
            if ( $helpers::get_parameter( 'post_type' ) ) {
                $args['post_type'] = $helpers::get_parameter( 'post_type' );
            }
            if ( $helpers::get_parameter( 'set_filter' ) ) {
                $args['set_filter'] = $helpers::get_parameter( 'set_filter' );
            }
            
            if ( $helpers::get_parameter( 'startDate' ) ) {
                $args['startDate'] = $helpers::get_parameter( 'startDate' );
                $args['endDate'] = $helpers::get_parameter( 'endDate' );
            }
            
            $allPosts = APVC_Query::get_dashboard_list_posts( 10, $args );
            ?>
		<div class="col-lg-12 col-md-12 col-12 mb-4 apvc-reports-block">
			<?php 
            self::get_advanced_filters_for_reports( $helpers, $args );
            ?>
			<div class="card">
				<div class="table-responsive text-nowrap pt-4 pb-10">
					<input type="hidden" id="apvc-reports-off" value="10">
					<input type="hidden" id="apvc-reports-pagi-end" value="0">
					<input type="hidden" id="apvc_post_type" value="<?php 
            if ( isset( $args['post_type'] ) ) {
                echo  $args['post_type'] ;
            }
            ?>">
					<table class="table reports-table-body">
						<thead>
						<tr>
							<th><?php 
            esc_html_e( 'Page ID', 'apvc' );
            ?></th>
							<th><?php 
            esc_html_e( 'Page Title', 'apvc' );
            ?></th>
							<th><?php 
            esc_html_e( 'Counts', 'apvc' );
            ?></th>
							<th><?php 
            esc_html_e( 'Post Type', 'apvc' );
            ?></th>
							<th><?php 
            esc_html_e( 'More data', 'apvc' );
            ?></th>
							<th><?php 
            esc_html_e( 'Initial Count Value', 'apvc' );
            ?></th>
						</tr>
						</thead>
						<tbody class="table-border-bottom-0 report-table-body">
						<?php 
            
            if ( !empty($allPosts) ) {
                foreach ( $allPosts as $post ) {
                    ?>
								<tr>
									<td><?php 
                    echo  $post->singular_id ;
                    ?></td>
									<td><?php 
                    echo  ( 45 < strlen( $post->stored_title ) ? substr( $post->stored_title, 0, 45 ) . '...' : $post->stored_title ) ;
                    ?> <a href="<?php 
                    echo  $post->stored_url ;
                    ?>" target="_blank">  <i class='bx bx-link-external'></i></a></td>
									<td>
                                        <strong><?php 
                    esc_html_e( 'Initial Count Value', 'apvc' );
                    ?>:</strong> <?php 
                    echo  APVC_Admin_Helpers::filter_count_format( APVC_Query::get_starting_count( $post->singular_id ) ) ;
                    ?><br />
                                        <strong><?php 
                    esc_html_e( 'Page Views', 'apvc' );
                    ?>: </strong><?php 
                    echo  APVC_Admin_Helpers::filter_count_format( $post->views ) ;
                    ?><br />
                                        <strong><?php 
                    esc_html_e( 'User Sessions', 'apvc' );
                    ?>: </strong><?php 
                    echo  APVC_Admin_Helpers::filter_count_format( APVC_Query::get_post_total_sessions( $post->singular_id ) ) ;
                    ?><br />
                                    </td>
									<td><?php 
                    echo  $post->stored_type_label ;
                    ?></td>
									<td><a href="<?php 
                    echo  APVC_DASHBOARD_PAGE_LINK ;
                    ?>&section=apvc-reports&sub_section=reports&p_id=<?php 
                    echo  $post->singular_id ;
                    ?>"><?php 
                    echo  esc_html_e( 'View more details', 'apvc' ) ;
                    ?></i></a></td>
									<td><a href="#" class="set_starting_count" data-id="<?php 
                    echo  $post->singular_id ;
                    ?>"><?php 
                    echo  esc_html_e( 'Set Starting Count', 'apvc' ) ;
                    ?></a></td>
								</tr>
								<?php 
                }
            } else {
                ?>
							<tr>
								<td colspan="9" class="text-center pt-5"><h5><?php 
                esc_html_e( 'No posts found!', 'apvc' );
                ?></h5></td>
							</tr>
						<?php 
            }
            
            ?>
						</tbody>
					</table>
				</div>
				<div class="row py-sm-4 gy-3 gy-sm-0 ">
					<div class="col apvc-load-more">
						<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin:auto;background:#fff;display:block;" width="75px" height="200px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
							<defs>
								<clipPath id="progress-y7p5500zcuj-cp" x="0" y="0" width="100" height="100">
									<rect x="0" y="0" width="0" height="100">
										<animate attributeName="width" repeatCount="indefinite" dur="1s" values="0;100;100" keyTimes="0;0.5;1"></animate>
										<animate attributeName="x" repeatCount="indefinite" dur="1s" values="0;0;100" keyTimes="0;0.5;1"></animate>
									</rect>
								</clipPath>
							</defs>
							<path fill="none" stroke="#5a8dee" stroke-width="2.79" d="M18 36.895L81.99999999999999 36.895A13.104999999999999 13.104999999999999 0 0 1 95.10499999999999 50L95.10499999999999 50A13.104999999999999 13.104999999999999 0 0 1 81.99999999999999 63.105L18 63.105A13.104999999999999 13.104999999999999 0 0 1 4.895000000000003 50L4.895000000000003 50A13.104999999999999 13.104999999999999 0 0 1 18 36.895 Z"></path>
							<path fill="#6610f2" clip-path="url(#progress-y7p5500zcuj-cp)" d="M18 40.99L82 40.99A9.009999999999998 9.009999999999998 0 0 1 91.00999999999999 50L91.00999999999999 50A9.009999999999998 9.009999999999998 0 0 1 82 59.01L18 59.01A9.009999999999998 9.009999999999998 0 0 1 8.990000000000004 50L8.990000000000004 50A9.009999999999998 9.009999999999998 0 0 1 18 40.99 Z"></path>
						</svg>
					</div>
				</div>
			</div>
		</div>
		<!-- Modal template -->
		<div class="modal modal-transparent fade" id="modals-set-starting-count" tabindex="-1">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-body sh_modal_body">
						<a
								href="javascript:void(0);"
								class="btn-close text-white"
								data-bs-dismiss="modal"
								aria-label="Close"
						></a>
						<div class="input-group input-group-lg mb-3">
							<input
									type="number" min="1"
									class="form-control bg-white border-0"
									id="inp_starting_count"
									oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
									maxlength="8"
									placeholder="<?php 
            echo  esc_html_e( 'Enter Initial Count Value', 'apvc' ) ;
            ?>"
							/>
							<input type="hidden" id="sc_post_id" value="0">
							<button class="btn btn-primary" type="button" id="set_starting_count"><?php 
            echo  esc_html_e( 'Set Starting Count', 'apvc' ) ;
            ?></button>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Modal template -->
		<div class="modal modal-transparent fade" id="modals-show-success-set-starting-count" tabindex="-1">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-body">
						<h3><?php 
            echo  esc_html_e( 'Initial Count set successfully.', 'apvc' ) ;
            ?></h3>
					</div>
				</div>
			</div>
		</div>
			<?php 
        }
    
    }
    
    public static function get_shortcodes_generator()
    {
        echo  '<div class="row"><div class="col-xl-2 col-lg-2 col-md-4 col-sm-4 col-6">' ;
        echo  '<a type="button" class="btn rounded-pill btn-primary" href="' . APVC_DASHBOARD_PAGE_LINK . '&section=apvc-shortcodes">' . __( 'Back to shortcode list', 'apvc' ) . ' ></a>' ;
        echo  '</div></div>' ;
        ?>
		<div class="apvc-shortocode-generator" id="apvc-shortcode-generator">
			<form id="sh_generate_frm" method="post">
			<div class="row">
				<div class="col-4">
					<div class="card mb-4">
						<h5 class="card-header"><?php 
        esc_html_e( 'Widget Settings', 'apvc' );
        ?></h5>
						<div class="card-body">
							<div class="row">
								<div class="col-md-12 mb-4">
									<label for="apvc-border-size" class="form-label"><?php 
        esc_html_e( 'Border Size(in pixels)', 'apvc' );
        ?></label>
									<input type="number" min="0" value="2" class="form-control" name="border_size">
								</div>

								<div class="col-md-12 mb-4">
									<label for="apvc-border-radius" class="form-label"><?php 
        esc_html_e( 'Border Radius(in pixels)', 'apvc' );
        ?></label>
									<input type="number" min="0" value="5" class="form-control" name="border_radius">
								</div>

								<div class="col-md-12 mb-4">
									<label for="apvc-border-style" class="form-label"><?php 
        esc_html_e( 'Border Style', 'apvc' );
        ?></label>
									<select name="border-style" class="form-select" style="max-width: 100%;">
										<option value="none"><?php 
        echo  esc_html_e( 'None', 'apvc' ) ;
        ?></option>
										<option value="dotted"><?php 
        echo  esc_html_e( 'Dotted', 'apvc' ) ;
        ?></option>
										<option value="dashed"><?php 
        echo  esc_html_e( 'Dashed', 'apvc' ) ;
        ?></option>
										<option value="solid" selected><?php 
        echo  esc_html_e( 'Solid', 'apvc' ) ;
        ?></option>
										<option value="double"><?php 
        echo  esc_html_e( 'Double', 'apvc' ) ;
        ?></option>
									</select>
								</div>

								<div class="col-md-12 mb-4">
									<label for="apvc-border-color" class="form-label"><?php 
        esc_html_e( 'Border Color', 'apvc' );
        ?></label>
									<div class="classic col col-sm-3 col-lg-2">
										<div id="border_color"></div>
										<input type="hidden" name="border_color" value="#000000">
									</div>
								</div>

								<div class="col-md-12 mb-4">
									<label for="apvc-font-color" class="form-label"><?php 
        esc_html_e( 'Text Color', 'apvc' );
        ?></label>
									<div class="classic col col-sm-3 col-lg-2">
										<div id="font_color"></div>
										<input type="hidden" name="font_color" value="#000000">
									</div>
								</div>

								<div class="col-md-12 mb-4">
									<label for="apvc-background-color" class="form-label"><?php 
        esc_html_e( 'Background Color', 'apvc' );
        ?></label>
									<div class="classic col col-sm-3 col-lg-2">
										<div id="background_color"></div>
										<input type="hidden" name="background_color" value="#e2e2e2">
									</div>
								</div>

								<div class="col-md-12 mb-4">
									<label for="apvc-font-style" class="form-label"><?php 
        esc_html_e( 'Font Style', 'apvc' );
        ?></label>
									<select name="font-style" class="form-select" style="max-width: 100%;">
										<option value="normal"><?php 
        echo  esc_html_e( 'Normal', 'apvc' ) ;
        ?></option>
										<option value="bold"><?php 
        echo  esc_html_e( 'Bold', 'apvc' ) ;
        ?></option>
										<option value="italic"><?php 
        echo  esc_html_e( 'Italic', 'apvc' ) ;
        ?></option>
									</select>
								</div>

								<div class="col-md-12 mb-4">
									<label for="apvc-padding" class="form-label"><?php 
        esc_html_e( 'Padding', 'apvc' );
        ?></label>
									<input type="number" min="0" value="5" class="form-control" id="padding" name="padding">
								</div>

								<div class="col-md-12 mb-4">
									<label for="apvc-width" class="form-label"><?php 
        esc_html_e( 'Width', 'apvc' );
        ?></label>
									<input type="number" min="0" step="10" value="200" class="form-control" id="width" name="width">
								</div>

								<div class="col-12 d-flex justify-content-between">
									<button type="button" class="btn btn-primary btn-submit apvc-generate-shortcode"><?php 
        esc_html_e( 'Generate Shortcode', 'apvc' );
        ?></button>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-4 shortcode-ad-settings">
					<div class="card mb-4">
						<h5 class="card-header"><?php 
        esc_html_e( 'Advanced Settings', 'apvc' );
        ?></h5>
						<div class="card-body">
							<div class="row">
								<div class="col-md-12 mb-4">
									<div class="form-check mt-3">
										<label class="form-check-label" for="show_icon"><?php 
        esc_html_e( 'Show Icon', 'apvc' );
        ?></label>
										<input class="form-check-input" type="checkbox" id="show_icon" name="show_icon" /><br />
										<small><?php 
        echo  esc_html_e( '*This will show icons before states labels. (If template is used then this setting will be ignored.)', 'apvc' ) ;
        ?></small>
									</div>
								</div>

								<div class="col-md-12 mb-4">
									<div class="form-check mt-3">
										<label class="form-check-label" for="show_today_visits"><?php 
        esc_html_e( "Show Today's Visit Counts", 'apvc' );
        ?></label>
										<input class="form-check-input" checked type="checkbox" id="show_today_visits" name="show_today_visits" /><br />
										<small><?php 
        echo  esc_html_e( "*This will show today's count for individual post/page.", 'apvc' ) ;
        ?></small>
									</div>
								</div>

								<div class="col-md-12 mb-4">
									<label for="today_counter_label" class="form-label"><?php 
        esc_html_e( "Today's Counter Label", 'apvc' );
        ?></label>
									<input type="text" class="form-control" name="today_counter_label" value="Today:">
								</div>

								<div class="col-md-12 mb-4">
									<div class="form-check mt-3">
										<label class="form-check-label" for="show_global_visits"><?php 
        esc_html_e( 'Show Global Total Counts', 'apvc' );
        ?></label>
										<input class="form-check-input" checked type="checkbox" id="show_global_visits" name="show_global_visits" /><br />
										<small><?php 
        echo  esc_html_e( '*This will show total counts for whole website.', 'apvc' ) ;
        ?></small>
									</div>
								</div>

								<div class="col-md-12 mb-4">
									<label for="global_counter_label" class="form-label"><?php 
        esc_html_e( 'Global Counter Label', 'apvc' );
        ?></label>
									<input type="text" class="form-control" name="global_counter_label" value="Total:">
								</div>

								<div class="col-md-12 mb-4">
									<div class="form-check mt-3">
										<label class="form-check-label" for="show_current_total_visits"><?php 
        esc_html_e( 'Show Current Page Total', 'apvc' );
        ?></label>
										<input class="form-check-input" type="checkbox" checked id="show_current_total_visits" name="show_current_total_visits" /><br />
										<small><?php 
        echo  esc_html_e( '*This will show total counts the current page.', 'apvc' ) ;
        ?></small>
									</div>
								</div>

								<div class="col-md-12 mb-4">
									<label for="counter_label" class="form-label"><?php 
        esc_html_e( 'Counter Label for Total Visits', 'apvc' );
        ?></label>
									<input type="text" class="form-control" name="counter_label" value="Visits:">
								</div>

								<div class="col-md-12 mb-4">
									<label for="for_specific_post" class="form-label"><?php 
        esc_html_e( 'For specific post', 'apvc' );
        ?></label>
									<input type="number" class="form-control" name="for_specific_post" value="" placeholder="Enter post/page ID only">
								</div>

								<div class="col-md-12 mb-4">
									<label for="apvc-widget-template" class="form-label"><?php 
        esc_html_e( 'Widget Template', 'apvc' );
        ?></label>
									<select name="widget-template" class="form-select" style="max-width: 100%;">
										<option value=""><?php 
        echo  esc_html_e( 'Please Select', 'apvc' ) ;
        ?></option>
										<?php 
        $shortcodes = APVC_Admin_Helpers::get_shortcodes();
        if ( !empty($shortcodes) ) {
            foreach ( $shortcodes as $shortcode ) {
                ?>
												<option value="<?php 
                echo  $shortcode['key'] ;
                ?>"><?php 
                echo  $shortcode['name'] ;
                ?></option>
												<?php 
            }
        }
        ?>
									</select>
								</div>

								<!-- Modal template -->
								<div class="modal modal-transparent fade" id="modals-transparent" tabindex="-1">
									<div class="modal-dialog">
										<div class="modal-content">
											<div class="modal-body sh_modal_body">
												<a
														href="javascript:void(0);"
														class="btn-close text-white"
														data-bs-dismiss="modal"
														aria-label="Close"
												></a>
												<div class="input-group input-group-lg mb-3">
													<input
															type="text"
															class="form-control bg-white border-0"
															id="shortcode_name"
															placeholder="<?php 
        echo  esc_html_e( 'Enter Shortcode name', 'apvc' ) ;
        ?>"
													/>
													<button class="btn btn-primary" type="button" id="save_shortcode"><?php 
        echo  esc_html_e( 'Save Shortcode', 'apvc' ) ;
        ?></button>
												</div>
											</div>
										</div>
									</div>
								</div>

								<input type="hidden" id="generated_shortcode" value="">
								<div class="col-12 d-flex justify-content-between">
									<button type="button" class="btn btn-primary btn-submit apvc-generate-shortcode"><?php 
        esc_html_e( 'Generate Shortcode', 'apvc' );
        ?></button>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-4 shortcode-preview">
					<div class="card mb-4">
						<h5 class="card-header"><?php 
        esc_html_e( 'Shortcode Preview', 'apvc' );
        ?></h5>
						<div class="card-body">
							<div class="row">
								<div class="col-md-12 mb-4" id="apvc_shortcode_preview">
									<div class="m-2 show_shortcode" id="show_shortcode"></div>
									<div class="show_generated_shortcode text-center" id="show_generated_shortcode"></div>
								</div>

								<div class="col-12 d-flex">
									<button type="button" id="apvc-save-shortcode" data-bs-toggle="modal" data-bs-target="#modals-transparent" class="btn btn-primary btn-submit m-1" disabled><?php 
        esc_html_e( 'Save Shortcode', 'apvc' );
        ?></button>
<!--									<button type="button" id="apvc-copy-shortcode" class="btn btn-primary btn-submit m-1" disabled>--><?php 
        //esc_html_e( 'Copy Shortcode', 'apvc' );
        ?><!--</button>-->
									<button type="button" id="apvc-reset-shortcode" class="btn btn-primary btn-submit m-1"><?php 
        esc_html_e( 'Reset', 'apvc' );
        ?></button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			</form>
		</div>
		<?php 
    }
    
    public static function get_referrers_page()
    {
        
        if ( isset( $_POST['apvc_refresh_referrers'] ) ) {
            delete_transient( 'apvc_trend_refferer' );
            delete_transient( 'apvc_trend_refferer_list' );
            echo  '<script>window.location.reload();</script>' ;
        }
        
        ?>
		<input type="hidden" value="refer" id="apvc_page_id">
		<div id="apvc-overlay"></div>
			<div class="accordion accordion-header-primary" id="apvc_advanced_filters">
				<div class="accordion-item card">
					<h2 class="accordion-header">
						<button type="button" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#apvc-advanced-filters" aria-expanded="false">
							<?php 
        esc_html_e( 'Advanced Filters', 'apvc' );
        ?>&nbsp;&nbsp;<i class="bx bx-calendar"></i>
						</button>
					</h2>
					<div id="apvc-advanced-filters" class="accordion-collapse collapse" data-bs-parent="#apvc-advanced-filters" style="">
						<div class="accordion-body">
							<div class="col-4">
								<label for="bs-rangepicker-range" class="form-label"><?php 
        esc_html_e( 'Date Ranges', 'apvc' );
        ?></label>
								<input type="text" id="bs-rangepicker-range" class="form-control" />
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php 
        self::get_total_visitors( 'refer' );
        self::get_total_views( 'refer' );
        self::get_total_user_sessions( 'refer' );
        self::get_trending_referrer();
        self::get_main_chart();
        self::get_referrer_list();
    }
    
    public static function get_trending_page()
    {
        echo  '<input type="hidden" value="trending" id="apvc_page_id">' ;
        echo  '<form action="" method="POST">' ;
        echo  '<div class="col-lg-12 col-md-12 col-12" style="margin-bottom: -25px;">' ;
        echo  '<div class="alert alert-primary" role="alert">' . __( '*The data will be updated every hour. ', 'apvc' ) . '  <input style="padding: 5px;" name="apvc_refresh_trends" class="btn btn-primary btn-submit" type="submit" value="' . __( 'Refresh data now', 'apvc' ) . '"></div>' ;
        echo  '</div>' ;
        echo  '</form>' ;
        
        if ( isset( $_POST['apvc_refresh_trends'] ) ) {
            delete_transient( 'apvc_trend_top_pages' );
            delete_transient( 'apvc_trend_top_posts' );
            delete_transient( 'apvc_trend_top_countries' );
            delete_transient( 'apvc_trend_top_states' );
            delete_transient( 'apvc_trend_top_cities' );
            delete_transient( 'apvc_trend_top_devices' );
            delete_transient( 'apvc_trend_top_devices_os' );
            delete_transient( 'apvc_trend_top_browsers' );
            echo  '<script>window.location.reload();</script>' ;
        }
        
        self::get_top_ten_pages();
        self::get_top_ten_posts();
        self::get_top_ten_country();
        self::get_top_ten_states();
        self::get_top_ten_cities();
        self::get_top_ten_devices();
        self::get_top_ten_devices_os();
        self::get_top_ten_browsers();
    }
    
    public static function get_top_ten_pages()
    {
        ?>
		<div class="col-md-12 mb-4 tr_top_articles">
			<div class="card" style="min-height: 500px;">
				<h5 class="card-header"><?php 
        esc_html_e( 'Top 10 Pages', 'apvc' );
        ?></h5>
				<div class="table-responsive text-nowrap">
					<table class="table">
						<thead>
						<tr>
							<th><?php 
        esc_html_e( 'Page ID', 'apvc' );
        ?></th>
							<th><?php 
        esc_html_e( 'Title', 'apvc' );
        ?></th>
							<th><?php 
        esc_html_e( 'Views', 'apvc' );
        ?></th>
							<th><?php 
        esc_html_e( 'Visitors', 'apvc' );
        ?></th>
							<th><?php 
        esc_html_e( 'User Sessions', 'apvc' );
        ?></th>
						</tr>
						</thead>
						<tbody class="table-border-bottom-0 tr_top_articles_body">
							<tr>
								<td colspan="5" class="text-center pt-5"><h5><?php 
        esc_html_e( 'No pages found!', 'apvc' );
        ?></h5></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<?php 
    }
    
    public static function get_top_ten_posts()
    {
        ?>
		<div class="col-md-12 col-12 mb-4 tr_top_posts">
			<div class="card" style="min-height: 500px;">
				<h5 class="card-header"><?php 
        esc_html_e( 'Top 10 Posts', 'apvc' );
        ?></h5>
				<div class="table-responsive text-nowrap">
					<table class="table">
						<thead>
						<tr>
							<th><?php 
        esc_html_e( 'Post ID', 'apvc' );
        ?></th>
							<th><?php 
        esc_html_e( 'Title', 'apvc' );
        ?></th>
							<th><?php 
        esc_html_e( 'Views', 'apvc' );
        ?></th>
							<th><?php 
        esc_html_e( 'Visitors', 'apvc' );
        ?></th>
							<th><?php 
        esc_html_e( 'User Sessions', 'apvc' );
        ?></th>
						</tr>
						</thead>
						<tbody class="table-border-bottom-0 tr_top_posts_body">
							<tr>
								<td colspan="5" class="text-center pt-5"><h5><?php 
        esc_html_e( 'No posts found!', 'apvc' );
        ?></h5></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<?php 
    }
    
    public static function get_top_ten_country()
    {
        ?>
		<div class="col-md-12 col-12 mb-4 tr_top_countries">
			<div class="card" style="min-height: 500px;">
				<h5 class="card-header"><?php 
        esc_html_e( 'Top 10 Country', 'apvc' );
        ?></h5>
				<div class="table-responsive text-nowrap">
					<table class="table">
						<thead>
						<tr>
							<th><?php 
        esc_html_e( 'Country', 'apvc' );
        ?></th>
							<th><?php 
        esc_html_e( 'Views', 'apvc' );
        ?></th>
							<th><?php 
        esc_html_e( 'Visitors', 'apvc' );
        ?></th>
							<th><?php 
        esc_html_e( 'User Sessions', 'apvc' );
        ?></th>
						</tr>
						</thead>
						<tbody class="table-border-bottom-0 tr_top_countries_body">
							<tr>
								<td colspan="4" class="text-center pt-5"><h5><?php 
        esc_html_e( 'No countries found!', 'apvc' );
        ?></h5></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<?php 
    }
    
    public static function get_top_ten_states()
    {
        ?>
		<div class="col-md-12 col-12 mb-4 tr_top_states">
			<div class="card" style="min-height: 500px;">
				<h5 class="card-header"><?php 
        esc_html_e( 'Top 10 States/Subdivision', 'apvc' );
        ?></h5>
				<div class="table-responsive text-nowrap">
					<table class="table">
						<thead>
						<tr>
							<th><?php 
        esc_html_e( 'State/Subdivision', 'apvc' );
        ?></th>
							<th><?php 
        esc_html_e( 'Views', 'apvc' );
        ?></th>
							<th><?php 
        esc_html_e( 'Visitors', 'apvc' );
        ?></th>
							<th><?php 
        esc_html_e( 'User Sessions', 'apvc' );
        ?></th>
						</tr>
						</thead>
						<tbody class="table-border-bottom-0 tr_top_states_body">
							<tr>
								<td colspan="4" class="text-center pt-5"><h5><?php 
        esc_html_e( 'No states found!', 'apvc' );
        ?></h5></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<?php 
    }
    
    public static function get_top_ten_cities()
    {
        ?>
		<div class="col-md-12 col-12 mb-4 tr_top_city">
			<div class="card" style="min-height: 500px;">
				<h5 class="card-header"><?php 
        esc_html_e( 'Top 10 Cities', 'apvc' );
        ?></h5>
				<div class="table-responsive text-nowrap">
					<table class="table">
						<thead>
						<tr>
							<th><?php 
        esc_html_e( 'City', 'apvc' );
        ?></th>
							<th><?php 
        esc_html_e( 'Views', 'apvc' );
        ?></th>
							<th><?php 
        esc_html_e( 'Visitors', 'apvc' );
        ?></th>
							<th><?php 
        esc_html_e( 'User Sessions', 'apvc' );
        ?></th>
						</tr>
						</thead>
						<tbody class="table-border-bottom-0 tr_top_city_body">
							<tr>
								<td colspan="4" class="text-center pt-5"><h5><?php 
        esc_html_e( 'No cities found!', 'apvc' );
        ?></h5></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<?php 
    }
    
    public static function get_top_ten_devices()
    {
        ?>
		<div class="col-md-12 col-12 mb-4 tr_top_devices">
			<div class="card" style="min-height: 500px;">
				<h5 class="card-header"><?php 
        esc_html_e( 'Top 10 Devices', 'apvc' );
        ?></h5>
				<div class="table-responsive text-nowrap">
					<table class="table">
						<thead>
						<tr>
							<th><?php 
        esc_html_e( 'Device', 'apvc' );
        ?></th>
							<th><?php 
        esc_html_e( 'Views', 'apvc' );
        ?></th>
							<th><?php 
        esc_html_e( 'Visitors', 'apvc' );
        ?></th>
							<th><?php 
        esc_html_e( 'User Sessions', 'apvc' );
        ?></th>
						</tr>
						</thead>
						<tbody class="table-border-bottom-0 tr_top_devices_body">
							<tr>
								<td colspan="4" class="text-center pt-5"><h5><?php 
        esc_html_e( 'No devices found!', 'apvc' );
        ?></h5></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<?php 
    }
    
    public static function get_top_ten_devices_os()
    {
        ?>
		<div class=" col-md-12 col-12 mb-4 tr_top_devices_os">
			<div class="card" style="min-height: 500px;">
				<h5 class="card-header"><?php 
        esc_html_e( 'Top 10 Operating Systems', 'apvc' );
        ?></h5>
				<div class="table-responsive text-nowrap">
					<table class="table">
						<thead>
						<tr>
							<th><?php 
        esc_html_e( 'Operating System', 'apvc' );
        ?></th>
							<th><?php 
        esc_html_e( 'Views', 'apvc' );
        ?></th>
							<th><?php 
        esc_html_e( 'Visitors', 'apvc' );
        ?></th>
							<th><?php 
        esc_html_e( 'User Sessions', 'apvc' );
        ?></th>
						</tr>
						</thead>
						<tbody class="table-border-bottom-0 tr_top_devices_os_body">
							<tr>
								<td colspan="4" class="text-center pt-5"><h5><?php 
        esc_html_e( 'No operating systems found!', 'apvc' );
        ?></h5></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<?php 
    }
    
    public static function get_top_ten_browsers()
    {
        ?>
		<div class="col-md-12 col-12 mb-4 tr_top_devices_browsers">
			<div class="card" style="min-height: 500px;">
				<h5 class="card-header"><?php 
        esc_html_e( 'Top 10 Browsers', 'apvc' );
        ?></h5>
				<div class="table-responsive text-nowrap">
					<table class="table">
						<thead>
						<tr>
							<th><?php 
        esc_html_e( 'Browser', 'apvc' );
        ?></th>
							<th><?php 
        esc_html_e( 'Views', 'apvc' );
        ?></th>
							<th><?php 
        esc_html_e( 'Visitors', 'apvc' );
        ?></th>
							<th><?php 
        esc_html_e( 'User Sessions', 'apvc' );
        ?></th>
						</tr>
						</thead>
						<tbody class="table-border-bottom-0 tr_top_devices_browsers_body">
							<tr>
								<td colspan="4" class="text-center pt-5"><h5><?php 
        esc_html_e( 'No operating systems found!', 'apvc' );
        ?></h5></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<?php 
    }
    
    public static function get_referrer_list()
    {
        echo  '<form action="" method="POST">' ;
        echo  '<div class="col-lg-12 col-md-6 col-12 " style="margin-bottom: -25px;">' ;
        echo  '<div class="alert alert-primary" role="alert">' . __( '*The data will be updated every hour.', 'apvc' ) . ' <input style="padding: 5px;" name="apvc_refresh_referrers" class="btn btn-primary btn-submit" type="submit" value="' . __( 'Refresh data now', 'apvc' ) . '"></div>' ;
        echo  '</div>' ;
        echo  '</form>' ;
        ?>
		<div class="col-lg-12 col-md-6 col-12 mb-2 tr_top_referrers_list">
			<div class="card">
				<div class="table-responsive text-nowrap">
					<table class="table">
						<thead>
						<tr>
							<th><?php 
        esc_html_e( 'Referrer', 'apvc' );
        ?></th>
							<th><?php 
        esc_html_e( 'Type', 'apvc' );
        ?></th>
							<th><?php 
        esc_html_e( 'Visitors', 'apvc' );
        ?></th>
							<th><?php 
        esc_html_e( 'Total Views', 'apvc' );
        ?></th>
						</tr>
						</thead>
						<tbody class="table-border-bottom-0 tr_top_referrers_list_body">
							<tr>
								<td colspan="7" class="text-center pt-5"><h5><?php 
        esc_html_e( 'No posts found!', 'apvc' );
        ?></h5></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<?php 
    }
    
    public static function get_realtime_stats()
    {
        do_action( 'apvc_realtime_stats' );
    }
    
    public static function get_smart_campaigns()
    {
        do_action( 'apvc_smart_campaigns' );
    }
    
    public static function get_smart_campaigns_creator()
    {
        do_action( 'apvc_campaign_create' );
    }
    
    public static function get_wc_stats()
    {
        
        if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            do_action( 'apvc_wc_stats' );
        } else {
            die( __( 'You have not enough rights to access this page.', 'apvc' ) );
        }
    
    }
    
    public static function get_export_data()
    {
        do_action( 'apvc_export_data' );
    }
    
    public static function get_export_wizard()
    {
        do_action( 'apvc_export_wizard' );
    }
    
    public static function get_promotions_blocks()
    {
        ?>
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card overflow-hidden">


                <!-- Popular Articles -->
                <div class="help-center-popular-articles py-5">
                    <div class="container">
                        <h4 class="text-center mt-2 pb-3"><?php 
        echo  esc_html_e( 'Premium Features', 'apvc' ) ;
        ?></h4>
                        <div class="row">
                            <div class="col-lg-10 mx-auto">
                                <div class="row mb-3">

                                    <div class="col-md-4 mb-md-0 mb-4">
                                        <div class="card border shadow-none">
                                            <div class="card-body text-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zM4 18V6h16v12z"></path><path d="M6 8h2v8H6zm3 0h1v8H9zm8 0h1v8h-1zm-4 0h3v8h-3zm-2 0h1v8h-1z"></path></svg>
                                                <h5 class="mt-2"><?php 
        echo  esc_html_e( 'Shortcodes', 'apvc' ) ;
        ?></h5>
                                                <p><?php 
        echo  esc_html_e( 'Upgrade to our premium version and gain access to special shortcode blocks you won\'t find anywhere else.', 'apvc' ) ;
        ?></p>
                                                <a class="btn btn-label-primary" target="_blank" href="https://pagevisitcounter.com/shortcode-templates/"><?php 
        echo  esc_html_e( 'Read More', 'apvc' ) ;
        ?></a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-md-0 mb-4">
                                        <div class="card border shadow-none">
                                            <div class="card-body text-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="M3 3v17a1 1 0 0 0 1 1h17v-2H5V3H3z"></path><path d="M15.293 14.707a.999.999 0 0 0 1.414 0l5-5-1.414-1.414L16 12.586l-2.293-2.293a.999.999 0 0 0-1.414 0l-5 5 1.414 1.414L13 12.414l2.293 2.293z"></path></svg>
                                                <h5 class="mt-2"><?php 
        echo  esc_html_e( 'Real-time Statistics', 'apvc' ) ;
        ?></h5>
                                                <p><?php 
        echo  esc_html_e( 'Real-time statistics, instant access. Stay informed, make better decisions with up-to-date data for valuable insights.', 'apvc' ) ;
        ?></p>
                                                <a class="btn btn-label-primary" target="_blank" href="https://pagevisitcounter.com/real-time-statistics/"><?php 
        echo  esc_html_e( 'Read More', 'apvc' ) ;
        ?></a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-md-0 mb-4">
                                        <div class="card border shadow-none">
                                            <div class="card-body text-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="M21 4H2v2h2.3l3.521 9.683A2.004 2.004 0 0 0 9.7 17H18v-2H9.7l-.728-2H18c.4 0 .762-.238.919-.606l3-7A.998.998 0 0 0 21 4z"></path><circle cx="10.5" cy="19.5" r="1.5"></circle><circle cx="16.5" cy="19.5" r="1.5"></circle></svg>
                                                <h5 class="mt-2"><?php 
        echo  esc_html_e( 'eCommerce Statistics', 'apvc' ) ;
        ?></h5>
                                                <p><?php 
        echo  esc_html_e( 'Ecommerce stats from WooCommerce and more plugins, new integrations coming soon. Stay informed for insights!', 'apvc' ) ;
        ?></p>
                                                <a class="btn btn-label-primary" target="_blank" href="https://pagevisitcounter.com/ecommerce-statistics/"><?php 
        echo  esc_html_e( 'Read More', 'apvc' ) ;
        ?></a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-md-0 mb-4">
                                        <div class="card border shadow-none">
                                            <div class="card-body text-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="M13 6c2.507.423 4.577 2.493 5 5h4c-.471-4.717-4.283-8.529-9-9v4z"></path><path d="M18 13c-.478 2.833-2.982 4.949-5.949 4.949-3.309 0-6-2.691-6-6C6.051 8.982 8.167 6.478 11 6V2c-5.046.504-8.949 4.773-8.949 9.949 0 5.514 4.486 10 10 10 5.176 0 9.445-3.903 9.949-8.949h-4z"></path></svg>
                                                <h5 class="mt-2"><?php 
        echo  esc_html_e( 'Campaign Statistics', 'apvc' ) ;
        ?></h5>
                                                <p><?php 
        echo  esc_html_e( 'Track your campaign statistics and create extended campaigns with short URLs for better insights and performance.', 'apvc' ) ;
        ?></p>
                                                <a class="btn btn-label-primary" target="_blank" href="https://pagevisitcounter.com/campaign-builder-and-statistics/"><?php 
        echo  esc_html_e( 'Read More', 'apvc' ) ;
        ?></a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-md-0 mb-4">
                                        <div class="card border shadow-none">
                                            <div class="card-body text-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="M11 16h2V7h3l-4-5-4 5h3z"></path><path d="M5 22h14c1.103 0 2-.897 2-2v-9c0-1.103-.897-2-2-2h-4v2h4v9H5v-9h4V9H5c-1.103 0-2 .897-2 2v9c0 1.103.897 2 2 2z"></path></svg>
                                                <h5 class="mt-2"><?php 
        echo  esc_html_e( 'Export Data', 'apvc' ) ;
        ?></h5>
                                                <p><?php 
        echo  esc_html_e( 'Effortlessly export campaign stats with geo-location, device data, post fields, and referrer details included.', 'apvc' ) ;
        ?></p>
                                                <a class="btn btn-label-primary" target="_blank" href="https://pagevisitcounter.com/export-data/"><?php 
        echo  esc_html_e( 'Read More', 'apvc' ) ;
        ?></a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-md-0 mb-4">
                                        <div class="card border shadow-none">
                                            <div class="card-body text-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="m2.344 15.271 2 3.46a1 1 0 0 0 1.366.365l1.396-.806c.58.457 1.221.832 1.895 1.112V21a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1v-1.598a8.094 8.094 0 0 0 1.895-1.112l1.396.806c.477.275 1.091.11 1.366-.365l2-3.46a1.004 1.004 0 0 0-.365-1.366l-1.372-.793a7.683 7.683 0 0 0-.002-2.224l1.372-.793c.476-.275.641-.89.365-1.366l-2-3.46a1 1 0 0 0-1.366-.365l-1.396.806A8.034 8.034 0 0 0 15 4.598V3a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v1.598A8.094 8.094 0 0 0 7.105 5.71L5.71 4.904a.999.999 0 0 0-1.366.365l-2 3.46a1.004 1.004 0 0 0 .365 1.366l1.372.793a7.683 7.683 0 0 0 0 2.224l-1.372.793c-.476.275-.641.89-.365 1.366zM12 8c2.206 0 4 1.794 4 4s-1.794 4-4 4-4-1.794-4-4 1.794-4 4-4z"></path></svg>
                                                <h5 class="mt-2"><?php 
        echo  esc_html_e( 'Premium Settings', 'apvc' ) ;
        ?></h5>
                                                <p><?php 
        echo  esc_html_e( 'Enjoy exclusive settings, such as blocking users by country and hiding widgets based on users and user roles.', 'apvc' ) ;
        ?></p>
                                                <a class="btn btn-label-primary" target="_blank" href="https://pagevisitcounter.com/premium-settings/"><?php 
        echo  esc_html_e( 'Read More', 'apvc' ) ;
        ?></a>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pricing-free-trial">
                        <div class="container">
                            <div class="col-md-12">
                                <div class="text-center mt-5 mb-5">
                                    <h3 class="text-primary"><?php 
        echo  esc_html_e( 'Exciting surprises ahead! Get ready for a wave of new features coming your way very soon!', 'apvc' ) ;
        ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pricing-free-trial" style="background-color: rgba(90, 141, 238, 0.08); padding: 50px;">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="text-center mt-5">
                                        <h3 class="text-primary"><?php 
        echo  esc_html_e( "Hurry! Buy now at a 30% discount, but act quickly, as this offer won't last long.", 'apvc' ) ;
        ?></h3>
                                        <p class="fs-5">
                                            <?php 
        echo  esc_html_e( "Don't miss out on amazing premium features!", 'apvc' ) ;
        ?><br />
                                            <?php 
        echo  esc_html_e( " Buy now and enhance your experience like never before.", 'apvc' ) ;
        ?>
                                        </p>
                                        <a href="https://checkout.freemius.com/mode/dialog/plugin/5937/plan/9731/licenses/1/" class="btn btn-primary my-md-3"><?php 
        echo  esc_html_e( "Buy Now", 'apvc' ) ;
        ?></a>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-center mt-5">
                                        <h3 class="text-primary"><?php 
        echo  esc_html_e( "Still not convinced? Start with a 5-day FREE trial!", 'apvc' ) ;
        ?></h3>
                                        <p class="fs-5"><?php 
        echo  esc_html_e( "You will get full access to with all the features for 5 days.", 'apvc' ) ;
        ?></p>
                                        <a href="https://checkout.freemius.com/mode/dialog/plugin/5937/plan/9731/?trial=paid" class="btn btn-primary my-3 my-md-5"><?php 
        echo  esc_html_e( "Start 5-day FREE trial", 'apvc' ) ;
        ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Popular Articles -->

                <!-- Help Area -->
                <div class="help-center-contact-us help-center-bg-alt">
                    <div class="container-xl">
                        <div class="row justify-content-center py-5 my-3">
                            <div class="col-md-8 col-lg-6 text-center">
                                <h4><?php 
        echo  esc_html_e( "Still need help?", 'apvc' ) ;
        ?></h4>
                                <p class="mb-4">
	                                <?php 
        echo  esc_html_e( "Our specialists are always happy to help. Contact us during standard", 'apvc' ) ;
        ?>
                                    <br>
                                    <?php 
        echo  esc_html_e( "business hours or email us 24/7 and we'll get back to you.", 'apvc' ) ;
        ?>
                                </p>
                                <div class="d-flex justify-content-center flex-wrap gap-4">
                                    <a href="https://helpdesk.pagevisitcounter.com" class="btn btn-label-primary" target="_blank"><?php 
        echo  esc_html_e( "Open Support Ticket", 'apvc' ) ;
        ?></a>
                                    <a href="https://pagevisitcounter.com/contact" class="btn btn-label-primary" target="_blank"><?php 
        echo  esc_html_e( "Contact us", 'apvc' ) ;
        ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Help Area -->

                <div class="pricing-faqs bg-alt-pricing rounded-bottom" style="background-color: rgba(90, 141, 238, 0.08);">
                    <div class="container py-5 px-lg-5">
                        <div class="row mt-0 mt-md-4">
                            <div class="col-12 text-center mb-4">
                                <h4 class="mb-2"><?php 
        echo  esc_html_e( "Frequently Asked Questions", 'apvc' ) ;
        ?></h4>
                                <p><?php 
        echo  esc_html_e( "Let us help answer the most common questions you might have.", 'apvc' ) ;
        ?></p>
                            </div>
                        </div>
                        <div class="row mx-3">
                            <div class="col-12">
                                <div id="faq" class="accordion accordion-header-primary">
                                    <div class="card accordion-item">
                                        <h6 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" aria-expanded="false" data-bs-target="#faq-1" aria-controls="faq-1">
	                                            <?php 
        echo  esc_html_e( 'Does this use Google Analytics?', 'apvc' ) ;
        ?>
                                            </button>
                                        </h6>
                                        <div id="faq-1" class="accordion-collapse collapse" data-bs-parent="#faq" style="">
                                            <div class="accordion-body">
	                                            <?php 
        echo  esc_html_e( 'The Advanced Page Visit Counter serves as a viable alternative to Google Analytics. It provides a robust solution for tracking and analyzing page visits, offering comparable functionalities and insights to enhance your understanding of user engagement on your website.', 'apvc' ) ;
        ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card accordion-item">
                                        <h6 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" aria-expanded="false" data-bs-target="#faq-2" aria-controls="faq-2">
				                                <?php 
        echo  esc_html_e( 'Can I use Advanced Page Visit Counter and Google Analytics at the same time?', 'apvc' ) ;
        ?>
                                            </button>
                                        </h6>
                                        <div id="faq-2" class="accordion-collapse collapse" data-bs-parent="#faq" style="">
                                            <div class="accordion-body">
				                                <?php 
        echo  esc_html_e( 'Yes, you can run them both simultaneously without any issues or errors.', 'apvc' ) ;
        ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card accordion-item">
                                        <h6 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" aria-expanded="false" data-bs-target="#faq-3" aria-controls="faq-3">
				                                <?php 
        echo  esc_html_e( 'Is this plugin works with latest WordPress versions?', 'apvc' ) ;
        ?>
                                            </button>
                                        </h6>
                                        <div id="faq-3" class="accordion-collapse collapse" data-bs-parent="#faq" style="">
                                            <div class="accordion-body">
				                                <?php 
        echo  esc_html_e( 'Yes, It is tested up to 6.3 WordPress version.', 'apvc' ) ;
        ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card accordion-item">
                                        <h6 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" aria-expanded="false" data-bs-target="#faq-4" aria-controls="faq-4">
				                                <?php 
        echo  esc_html_e( 'Will it slow down my site?', 'apvc' ) ;
        ?>
                                            </button>
                                        </h6>
                                        <div id="faq-4" class="accordion-collapse collapse" data-bs-parent="#faq" style="">
                                            <div class="accordion-body">
				                                <?php 
        echo  esc_html_e( 'No, Installing the Advanced Page Visit Counter will have a minimal impact on your site’s performance as the tracking script is less than 2kb in size and seamlessly integrated into the page. Therefore, you can enjoy the benefits of detailed tracking and analytics without experiencing any noticeable difference in your site’s speed or performance.', 'apvc' ) ;
        ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card accordion-item">
                                        <h6 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" aria-expanded="false" data-bs-target="#faq-5" aria-controls="faq-5">
				                                <?php 
        echo  esc_html_e( 'Does tracking start right away?', 'apvc' ) ;
        ?>
                                            </button>
                                        </h6>
                                        <div id="faq-5" class="accordion-collapse collapse" data-bs-parent="#faq" style="">
                                            <div class="accordion-body">
				                                <?php 
        echo  esc_html_e( 'Effortlessly install and activate the plug-and-play Advanced Page Visit Counter for a simple tracking solution on your website.', 'apvc' ) ;
        ?>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <?php 
    }

}