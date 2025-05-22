/**
 * @sw-package framework
 */

import { mount } from '@vue/test-utils';

async function createWrapper() {
    return mount(await wrapTestComponent('sw-empty-state'), {
        global: {
            stubs: {
                'sw-empty-state-deprecated': true,
            },
            mocks: {
                $route: {
                    meta: {
                        $module: {
                            icon: 'regular-content',
                            description: 'Foo bar',
                        },
                    },
                },
            },
        },
        props: {
            title: 'Oh no, nothing was found.',
        },
    });
}

describe('components/base/sw-empty-state', () => {
    it('should be a Vue.js component', async () => {
        global.activeFeatureFlags = ['v6.8.0.0'];
        const wrapper = await createWrapper();

        expect(wrapper.vm).toBeTruthy();
    });

    it('should render a title', async () => {
        global.activeFeatureFlags = ['v6.8.0.0'];
        const wrapper = await createWrapper();

        expect(wrapper.find('.mt-empty-state__headline').text()).toBe('Oh no, nothing was found.');
    });

    it('should render the module description', async () => {
        global.activeFeatureFlags = ['v6.8.0.0'];
        const wrapper = await createWrapper();

        expect(wrapper.find('.mt-empty-state__description').text()).toBe('Foo bar');
    });

    it('should render the subtitle instead of the module description', async () => {
        global.activeFeatureFlags = ['v6.8.0.0'];
        const wrapper = await createWrapper();

        await wrapper.setProps({
            subline: 'Alternative description',
        });

        expect(wrapper.find('.mt-empty-state__description').text()).toBe('Alternative description');
    });

    it('should be absolute by default', async () => {
        global.activeFeatureFlags = ['v6.8.0.0'];
        const wrapper = await createWrapper();

        expect(wrapper.classes()).toContain('sw-empty-state--absolute');
    });

    /**
     * @deprecated tag:v6.8.0 - will be removed
     */
    it('should be a Vue.js component in v6.7.0.0', async () => {
        Shopware.Utils.debug.warn = jest.fn();
        global.activeFeatureFlags = [];

        const wrapper = await createWrapper();

        expect(wrapper.vm).toBeTruthy();
    });

    /**
     * @deprecated tag:v6.8.0 - will be removed
     */
    it('should render the deprecated empty state in v6.7.0.0', async () => {
        Shopware.Utils.debug.warn = jest.fn();
        global.activeFeatureFlags = [];

        const wrapper = await createWrapper();
        const element = wrapper.find('.sw-empty-state--absolute');

        expect(Shopware.Utils.debug.warn).toHaveBeenCalled();
        expect(element.classes()).toContain('sw-empty-state--absolute');
        expect(element.element.getAttribute('title')).toBe('Oh no, nothing was found.');
        expect(element.element.getAttribute('icon')).toBe('regular-content');
    });
});
