<?php

namespace PGMB\Mustache;

class MustacheContextBuilder {
	/** @var MustacheContextProviderInterface[] */
	private array $providers;

	public function add_provider(MustacheContextProviderInterface $provider): self{
		$this->providers[] = $provider;
		return $this;
	}

	public function build(): array{
		$context = [];

		foreach($this->providers as $provider){
			$context[$provider->get_key()] = $provider->build();
		}

		return $context;
	}

	public function describe_all(): array{
		$descriptions = [];

		foreach($this->providers as $provider){
			$descriptions[$provider->get_key()] = $provider->describe();
		}

		return $descriptions;
	}
}